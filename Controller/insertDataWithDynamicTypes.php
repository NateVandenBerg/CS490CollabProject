<?php
class InsertSQL {
    public string $sql;
    public string $param_types = '';
    public array $insert_columns = [];

    // Methods
    /**
     * @param mixed $sql
     */
    public function setSql($sql): void
    {
        $this->sql = $sql;
    }
    /**
     * @return mixed
     */
    public function getSql()
    {
        return $this->sql;
    }
    /**
     * @param string $param_types
     */
    public function setParamTypes(string $param_types): void
    {
        $this->param_types = $param_types;
    }
    /**
     * @return string
     */
    public function getParamTypes(): string
    {
        return $this->param_types;
    }

    /**
     * @param array $insert_columns
     */
    public function setInsertColumns(array $insert_columns): void
    {
        $this->insert_columns = $insert_columns;
    }
    /**
     * @return array
     */
    public function getInsertColumns(): array
    {
        return $this->insert_columns;
    }
}

function buildQueryTableInsertAllColumns($conn, $tableName) {
    if (!$conn || !$tableName || !is_string($tableName)) {
        return false;  // Invalid input handling
    }

    $query = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $database = $conn->database;
    $stmt->bind_param('ss', $database, $tableName);
    $stmt->execute();
    $result = $stmt->get_result();

    $param_types = '';
//    $param_values = [];
    $insert_columns = [];

    while ($row = $result->fetch_assoc()) {
        $insert_columns[] = $row['COLUMN_NAME'];

        switch ($row['DATA_TYPE']) {
            case 'int':
            case 'tinyint':
            case 'smallint':
            case 'mediumint':
            case 'bigint':
                $param_types .= 'i';
                break;
            case 'double':
            case 'float':
            case 'decimal':
                $param_types .= 'd';
                break;
            case 'blob':
            case 'binary':
            case 'varbinary':
                $param_types .= 'b';
                break;
            default:
                $param_types .= 's';
        }
    }

    if (count($insert_columns) > 0) {
        $sql = "INSERT INTO " . $tableName . " (" . implode(", ", $insert_columns) . ") VALUES (" . str_repeat("?,", count($insert_columns) - 1) . "?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die('MySQL prepare error: ' . $conn->error);
        }
        return $stmt;
    }
}
function insertDataWithDynamicTypes($conn, $tableName, $escapedData) {
    echo "insertDataWithDynamicTypes.php";
    if (!$conn || !$tableName || !is_string($tableName) || !is_array($escapedData) || count($escapedData) === 0) {
        return false;  // Invalid input handling
    }

    $query = "SELECT COLUMN_NAME, DATA_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die('MySQL prepare error: ' . $conn->error);
    }

    $database = $conn->database;
    $stmt->bind_param('ss', $database, $tableName);
    $stmt->execute();
    $result = $stmt->get_result();

    $param_types = '';
    $param_values = [];
    $insert_columns = [];

    while ($row = $result->fetch_assoc()) {
        if (isset($escapedData[$row['COLUMN_NAME']])) {
            $insert_columns[] = $row['COLUMN_NAME'];
            $value = $escapedData[$row['COLUMN_NAME']];

            // Check if the value is an array and handle it accordingly
            if (is_array($value)) {
                $value = json_encode($value);
            }

            switch ($row['DATA_TYPE']) {
                case 'int':
                case 'tinyint':
                case 'smallint':
                case 'mediumint':
                case 'bigint':
                    $param_types .= 'i';
                    break;
                case 'double':
                case 'float':
                case 'decimal':
                    $param_types .= 'd';
                    break;
                case 'blob':
                case 'binary':
                case 'varbinary':
                    $param_types .= 'b';
                    break;
                default:
                    $param_types .= 's';
            }
            $param_values[] = $value;
        }
    }

    if (count($param_values) > 0) {
        $sql = "INSERT INTO $tableName (" . implode(", ", $insert_columns) . ") VALUES (" . str_repeat("?,", count($param_values) - 1) . "?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die('MySQL prepare error: ' . $conn->error);
        }

        $stmt->bind_param($param_types, ...$param_values);
        $stmt->execute();

        if ($stmt->error) {
            echo "Error: " . $stmt->error;
            $stmt->close();
            return false;
        } else {
            echo "Record inserted successfully";
            $stmt->close();
            return true;
        }
    } else {
        echo "No valid columns to insert.";
        $stmt->close();
        return false;
    }
}

function insertFacultyKeywords($conn, $tableName1, $tableName2, $keywords)
{
    if (!$conn || !$tableName1 || !is_string($tableName1) || !$tableName2 || !is_string($tableName2) || !is_array($keywords) || count($keywords) === 0) {
        return false;  // Invalid input handling
    }

    $userId = $conn->insert_id;

    foreach ($keywords as $keyword) {
        $tagSql = "INSERT IGNORE INTO " . $tableName1 . " (keyword) VALUES (?)";
        $tagStmt = $conn->prepare($tagSql);
        if ($tagStmt) {
            $tagStmt->bind_param("s", $keyword);
            $tagStmt->execute();
        }

        $relationSql = "INSERT INTO " . $tableName2 . " (user_id, keyword) VALUES (?, ?)";
        $relationStmt = $conn->prepare($relationSql);
        if ($relationStmt) {
            $relationStmt->bind_param("is", $userId, $keyword);
            $relationStmt->execute();
        }
    }
    return true;
}
