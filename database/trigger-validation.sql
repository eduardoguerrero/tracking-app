
DROP TRIGGER IF EXISTS trg_package_validate_in_transit;

CREATE DEFINER=`aeroflash`@`%` TRIGGER `trg_package_validate_in_transit` BEFORE UPDATE ON `packages` FOR EACH ROW BEGIN
                                                                                    IF NEW.status = 'In Transit' THEN
                                                                                    IF NEW.courier_id IS NULL THEN
                                                                                    SIGNAL SQLSTATE '45000'
                                                                                SET MESSAGE_TEXT = 'Cannot set status to In Transit: no courier assigned';
END IF;

        IF NEW.vehicle_id IS NULL THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: no vehicle assigned';
END IF;

        IF NOT EXISTS (
            SELECT 1 FROM couriers
            WHERE id = NEW.courier_id AND is_active = 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: assigned courier is not active';
END IF;

        IF NOT EXISTS (
            SELECT 1 FROM vehicles
            WHERE id = NEW.vehicle_id AND is_active = 1
        ) THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Cannot set status to In Transit: assigned vehicle is not active';
END IF;
END IF;
END