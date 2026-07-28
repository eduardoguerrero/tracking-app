-- =============================================================================
-- Aeroflash Tracking App
-- Módulo 1: Trigger Validation
-- Base de datos: MySQL 8.0
-- =============================================================================
-- Descripción: Trigger BEFORE UPDATE + BEFORE INSERT que valida que un paquete
-- no pueda cambiar a estado 'In Transit' sin tener asignado un repartidor y un
-- vehículo activos.
--
-- Defense in depth: esta validación ya existe a nivel aplicación
-- (App\Domain\Entities\Package + PackageStatusEnum + UpdatePackageStatusUseCase).
-- El trigger protege contra modificaciones directas a la base de datos que
-- bypasseen la API (ej: acceso directo a MySQL, backups mal restaurados).
-- =============================================================================

DROP TRIGGER IF EXISTS trg_package_validate_in_transit;

DELIMITER //

CREATE TRIGGER trg_package_validate_in_transit
BEFORE UPDATE ON packages
FOR EACH ROW
BEGIN
    -- Solo validar cuando el estado cambia a 'In Transit'
    IF NEW.status != 'In Transit' THEN
        -- No se requiere validación para otros estados
    ELSE
        -- Validar: courier asignado
        IF NEW.courier_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: no courier assigned';
        END IF;

        -- Validar: vehicle asignado
        IF NEW.vehicle_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: no vehicle assigned';
        END IF;

        -- Validar: courier está activo
        IF NOT EXISTS (
            SELECT 1 FROM couriers
            WHERE id = NEW.courier_id AND is_active = 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: assigned courier is not active';
        END IF;

        -- Validar: vehicle está activo
        IF NOT EXISTS (
            SELECT 1 FROM vehicles
            WHERE id = NEW.vehicle_id AND is_active = 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: assigned vehicle is not active';
        END IF;
    END IF;
END //

DELIMITER ;

-- =============================================================================
-- TRIGGER BEFORE INSERT (misma validación para paquetes nuevos)
-- =============================================================================

DROP TRIGGER IF EXISTS trg_package_insert_in_transit;

DELIMITER //

CREATE TRIGGER trg_package_insert_in_transit
BEFORE INSERT ON packages
FOR EACH ROW
BEGIN
    -- Solo validar si el paquete se inserta directamente en estado 'In Transit'
    IF NEW.status = 'In Transit' THEN
        IF NEW.courier_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot insert package with status In Transit: no courier assigned';
        END IF;

        IF NEW.vehicle_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot insert package with status In Transit: no vehicle assigned';
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM couriers
            WHERE id = NEW.courier_id AND is_active = 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot insert package with status In Transit: assigned courier is not active';
        END IF;

        IF NOT EXISTS (
            SELECT 1 FROM vehicles
            WHERE id = NEW.vehicle_id AND is_active = 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot insert package with status In Transit: assigned vehicle is not active';
        END IF;
    END IF;
END //

DELIMITER ;

-- =============================================================================
-- VERIFICACIÓN
-- =============================================================================
-- Para verificar que los triggers existen:
--
--   SHOW TRIGGERS LIKE 'packages';
--
-- Para probar (debería fallar):
--
--   -- Sin courier ni vehículo
--   UPDATE packages SET status = 'In Transit' WHERE tracking_number = 'AF-TEST-001';
--   -- ERROR 1644: Cannot set status to In Transit: no courier assigned
--
--   -- Con courier y vehículo válidos (debería funcionar)
--   UPDATE packages SET status = 'In Transit', courier_id = 1, vehicle_id = 1
--   WHERE tracking_number = 'AF-TEST-001';
--   -- OK
--
-- Para eliminar triggers:
--
--   DROP TRIGGER IF EXISTS trg_package_validate_in_transit;
--   DROP TRIGGER IF EXISTS trg_package_insert_in_transit;
-- =============================================================================
