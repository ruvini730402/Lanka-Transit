<?php
/**
 * Base Model Class
 * 
 * Abstract base class for all data models
 */

abstract class BaseModel {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    
    public function __construct() {
        $this->db = DatabaseConfig::getInstance()->getConnection();
    }
    
    /**
     * Find record by ID
     * 
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
            $stmt->execute([$id]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::find - " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find all records with optional conditions
     * 
     * @param array $conditions
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findAll(array $conditions = [], array $orderBy = [], int $limit = 0, int $offset = 0): array {
        try {
            $sql = "SELECT * FROM {$this->table}";
            $params = [];
            
            // Add WHERE conditions
            if (!empty($conditions)) {
                $whereClause = [];
                foreach ($conditions as $field => $value) {
                    $whereClause[] = "{$field} = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $whereClause);
            }
            
            // Add ORDER BY
            if (!empty($orderBy)) {
                $orderClause = [];
                foreach ($orderBy as $field => $direction) {
                    $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
                    $orderClause[] = "{$field} {$direction}";
                }
                $sql .= " ORDER BY " . implode(', ', $orderClause);
            }
            
            // Add LIMIT and OFFSET
            if ($limit > 0) {
                $sql .= " LIMIT {$limit}";
                if ($offset > 0) {
                    $sql .= " OFFSET {$offset}";
                }
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::findAll - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new record
     * 
     * @param array $data
     * @return int|false
     */
    public function create(array $data) {
        try {
            // Filter only fillable fields
            $filteredData = array_intersect_key($data, array_flip($this->fillable));
            
            if (empty($filteredData)) {
                return false;
            }
            
            $fields = array_keys($filteredData);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array_values($filteredData));
            
            return $result ? $this->db->lastInsertId() : false;
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::create - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update record by ID
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool {
        try {
            // Filter only fillable fields
            $filteredData = array_intersect_key($data, array_flip($this->fillable));
            
            if (empty($filteredData)) {
                return false;
            }
            
            $fields = array_keys($filteredData);
            $setClause = array_map(function($field) {
                return "{$field} = ?";
            }, $fields);
            
            $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE {$this->primaryKey} = ?";
            
            $params = array_values($filteredData);
            $params[] = $id;
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::update - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete record by ID
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
            return $stmt->execute([$id]);
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::delete - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count records with optional conditions
     * 
     * @param array $conditions
     * @return int
     */
    public function count(array $conditions = []): int {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            $params = [];
            
            if (!empty($conditions)) {
                $whereClause = [];
                foreach ($conditions as $field => $value) {
                    $whereClause[] = "{$field} = ?";
                    $params[] = $value;
                }
                $sql .= " WHERE " . implode(' AND ', $whereClause);
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return (int) $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::count - " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Execute custom query
     * 
     * @param string $sql
     * @param array $params
     * @return array
     */
    protected function query(string $sql, array $params = []): array {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Database error in " . get_class($this) . "::query - " . $e->getMessage());
            return [];
        }
    }
}
