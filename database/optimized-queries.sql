-- =============================================================================
-- Aeroflash Tracking App
-- Módulo 1: Optimized Queries & Indexes
-- Base de datos: MySQL 8.0
-- =============================================================================
-- Descripción: Consulta optimizada para obtener el total de paquetes entregados
-- por sucursal en el último mes, incluyendo el tiempo promedio de entrega en
-- horas. Diseñada para evitar Full Table Scans en tablas con 1M+ registros.
-- =============================================================================

-- =============================================================================
-- ÍNDICES SUGERIDOS (agregar antes de ejecutar la consulta en producción)
-- =============================================================================

-- Composite index: filtra por status, rango de fechas, y agrupa por branch.
-- Cubre WHERE, JOIN y GROUP BY en un solo índice, eliminando el Full Table Scan.
CREATE INDEX idx_packages_delivered_branch
    ON packages (status, delivered_at, branch_id);

-- Index auxiliar: optimiza el JOIN con branches y el filtro por fecha.
CREATE INDEX idx_branches_id_name
    ON branches (id, name);

-- =============================================================================
-- CONSULTA OPTIMIZADA
-- =============================================================================

SELECT
    b.name AS branch_name,
    COUNT(p.id) AS total_delivered,
    ROUND(
        AVG(TIMESTAMPDIFF(HOUR, p.created_at, p.delivered_at)), 2
    ) AS avg_delivery_hours
FROM packages p
INNER JOIN branches b ON b.id = p.branch_id
WHERE p.status = 'Delivered'
  AND p.delivered_at >= NOW() - INTERVAL 1 MONTH
GROUP BY b.id, b.name
ORDER BY total_delivered DESC;

-- =============================================================================
-- EXPLAIN (análisis del plan de ejecución)
-- =============================================================================
-- Sin el índice idx_packages_delivered_branch, MySQL haría:
--   type: ALL (Full Table Scan) sobre packages
--   rows: ~1,000,000+
--   Extra: Using where; Using temporary; Using filesort
--
-- Con el índice:
--   type: range sobre packages
--   key: idx_packages_delivered_branch
--   rows: solo los que cumplen status='Delivered' AND delivered_at reciente
--   Extra: Using index condition (no necesita leer la tabla completa)
--
-- Para verificar: EXPLAIN SELECT ... (ejecutar antes y después del índice)
-- =============================================================================

-- =============================================================================
-- STORED PROCEDURE (opcional — encapsula la lógica para reutilización)
-- =============================================================================

DROP PROCEDURE IF EXISTS GetDeliveredPackagesByBranch;

DELIMITER //

CREATE PROCEDURE GetDeliveredPackagesByBranch(IN months_back INT)
BEGIN
    SELECT
        b.name AS branch_name,
        COUNT(p.id) AS total_delivered,
        ROUND(
            AVG(TIMESTAMPDIFF(HOUR, p.created_at, p.delivered_at)), 2
        ) AS avg_delivery_hours
    FROM packages p
    INNER JOIN branches b ON b.id = p.branch_id
    WHERE p.status = 'Delivered'
      AND p.delivered_at >= NOW() - INTERVAL months_back MONTH
    GROUP BY b.id, b.name
    ORDER BY total_delivered DESC;
END //

DELIMITER ;

-- Uso: CALL GetDeliveredPackagesByBranch(1);  -- último mes
--       CALL GetDeliveredPackagesByBranch(3);  -- último trimestre
-- =============================================================================
