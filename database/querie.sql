CREATE INDEX idx_packages_delivered_branch
    ON packages (status, delivered_at, branch_id);

CREATE INDEX idx_branches_id_name
    ON branches (id, name);


DROP PROCEDURE IF EXISTS GetDeliveredPackagesByBranch;
--  CALL GetDeliveredPackagesByBranch(1);
CREATE PROCEDURE GetDeliveredPackagesByBranch(IN months_back INT)
BEGIN
    SELECT
        b.name AS branch_name,
        COUNT(p.id) AS total_delivered,
        ROUND(AVG(TIMESTAMPDIFF(HOUR, p.created_at, p.delivered_at)), 2) AS avg_delivery_hours
    FROM packages p
    INNER JOIN branches b ON b.id = p.branch_id
    WHERE p.status = 'Delivered'
      AND p.delivered_at >= NOW() - INTERVAL months_back MONTH
    GROUP BY b.id, b.name
    ORDER BY total_delivered DESC;
END //
