<?php
/*
 * db layer
 */

class DB 
{
  private $dbh;

  function __construct (array $params, array $options=[]) {
    $this->connect($params, $options);
    return $this->dbh;
  }

  public function connect (array $params, array $options=[]) {
    ( isset($params['driver']) ) ? $driver = $params['driver'] : $driver = 'sqlite';
    ( isset($params['host']) ) ? $host = $params['host'] : $host = 'localhost';
    $database = $params['database'];
    $user = $params['user'];
    $password = $params['password'];
    $opts = $options + array (
      PDO::ATTR_PERSISTENT => TRUE,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    try {
      $dsn = "$driver:host=$host;dbname=$database";
      $this->dbh = new PDO("$dsn", $user, $password, $opts);
      return $this->dbh;
    } catch (PDOException $e) {
        $this->dbh = NULL;
        die("$e->getMessage()");
    }
  }

  // get a dataset
  public function from ($table_refs) {
    return new Dataset($this->dbh, $table_refs);
  }

}

class Dataset
{
  private $dbh;
  private $sql_query = [];
  private $table_refs = '';   // tables 
  private $where_conditions = [];      // 'where' conditons
  /** Methods:
   * Methods that execute code on the database (should be called last in the 
   * chain):
   *  SELECT (All Records) *  all, each, 
   *  SELECT (First Record) *  first, last, get, []
   *  INSERT *  insert, 
   *  UPDATE *  update, set
   *  DELETE *  delete
   * Methods that return modified datasets:
   *  SELECT: select, select_all
   *  FROM *  from, from_self
   *  JOIN *  join, left_join, right_join, 
   *  WHERE *  where, filter, exclude,
   *  GROUP *  group, group_by,
   */

  function __construct ($dbh, $table_refs) {
    $this->dbh = $dbh;
    $this->table_refs = $table_refs;
    //$this->table_refs = ''; 
    $this->sql_query = [];
    $this->where_conditions = [];
    $this->from($table_refs);
  }

  public function from ($tables) {
    if ( ! isset($this->sql_query['operation']) )
      $this->select();
    if ( ! is_array($tables) ) {
      $this->table_refs = "$tables"; 
    } else { 
      $ndx = 0;
      foreach ($tables as $table) {
        if ($ndx == 0) {
          $this->table_refs = "$table";
        } else {
          $this->table_refs .= ", $table";
        }
        $ndx++;
      }
    }
    $this->sql_query['from'] = " FROM `$this->table_refs`";
    return clone($this);
  }

  public function select ($cols=NULL) {
    if ($cols) {
      $sql = "SELECT $cols";
    } else {
      $sql = "SELECT *";
    }
    $this->sql_query['operation'] = $sql;
    return $this;
  }

  public function where (array $conditions) {
    $this->where_conditions = $conditions;
    $ndx = 0;
    foreach ($conditions as $col => $val) {
      if ($ndx == 0) {
        $clause = "`$col` = :$col";
      } else {
        $clause .= " AND `$col` = :$col";
      }
      $ndx++;
    }
    $this->sql_query['where'] = " WHERE $clause";
    return $this;
  }

  /**
   * @return: PDOStatement
   */
  private function bind_where_conditions (PDOStatement $stmt) {
    if ($this->where_conditions) {  // where clause
      foreach ($this->where_conditions as $col => $val) {
        $stmt->bindValue(":$col", $val);
      }
      return $stmt;
    }
  }

  public function get_sql_query () {
    $sql = '';
    foreach ($this->sql_query as $key => $val) {
      $sql .= $val;
    }
    //printf("SQL: %s<br/>\n", $sql);
    return $sql;
  }
  public function all () {
    $sql = $this->get_sql_query();
    $stmt = $this->dbh->prepare($sql);
    if ($this->where_conditions)  // where clause
      $stmt = $this->bind_where_conditions($stmt);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public function insert (array $attribs) {
    $this->sql_query['operation'] = "INSERT INTO";
    $cols = '';
    $vals = '';
    $i = 0;
    foreach ($attribs as $col => $val) {
      $cols .= "$col";
      $vals .= ":$col";
      $i++;
      if ( count($attribs) > $i ) {
        $cols .= ", ";
        $vals .= ", ";
      }
    }
    $where_condition = ( ! empty($this->sql_query['where']) ) ? $this->sql_query['where'] : '';
    $sql = "INSERT INTO `$this->table_refs` ($cols) VALUES ($vals) $where_condition"; 
    printf("SQL: %s <br/>\n", $sql);
    $stmt = $this->dbh->prepare($sql);
    $stmt = $this->bind_where_conditions($stmt);
    // bind attribs
    foreach ($attribs as $col => $val) {
      $stmt->bindValue(":$col", $val);
    }
    return $this->execute($stmt);
  }

  public function update (array $attribs) {
    $this->sql_query['operation'] = "UPDATE";
    $attr = '';
    $i = 0;
    foreach ($attribs as $col => $val) {
      $i++;
      $attr .= "$col = :$col";
      if ( count($attribs) > $i ) 
        $attr .= ", ";
    }
    $where_condition = ( ! empty($this->sql_query['where']) ) ? $this->sql_query['where'] : '';
    $sql = "UPDATE `$this->table_refs` SET $attr $where_condition"; 
    printf("SQL: %s <br/>\n", $sql);
    $stmt = $this->dbh->prepare($sql);
    $stmt = $this->bind_where_conditions($stmt);
    foreach ($attribs as $col => $val) { // bind attribs
      $stmt->bindValue(":$col", $val);
    }
    return $this->execute($stmt);
  }

  public function delete ($cols=NULL) {
    if ($cols) {
      $sql = "DELETE $cols";
    } else {
      $sql = "DELETE *";
    }
    $this->sql_query['operation'] = $sql;
    $where_condition = ( ! empty($this->sql_query['where']) ) ? $this->sql_query['where'] : '';
    $sql = "DELETE FROM `$this->table_refs` $where_condition"; 
    printf("SQL: %s <br/>\n", $sql);
    $stmt = $this->dbh->prepare($sql);
    $stmt = $this->bind_where_conditions($stmt);
    return $this->execute($stmt);
  }

  // for insert, update, delete
  private function execute (PDOStatement $stmt) {
    $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->dbh->beginTransaction();
    try {
      $stmt->execute();
      return $this->dbh->commit();
    } catch (Exception $e) {
      $this->dbh->rollBack();
      printf("%s\n", $e->getMessage());
      return FALSE;
    }
  }

  public function join_table ($type, $other_table, array $conditions) {
    //$other_ds = self::new ($other_table); 
    if ($other_table instanceof $this) {
      $this->sql_query = [];
      // replace identifiers with their values
      $other_sql = $other_table->get_sql_query();
      $ids = $other_table->where_conditions;
      foreach ($ids as $key => $val) {
        $other_sql = str_replace(":$key", "'$val'", $other_sql);
      } 
      //printf("OtherSQL: %s\n", $other_sql);

      $this->sql_query['operation'] = "SELECT * ";
      $this->sql_query['from'] = "FROM `$this->table_refs` ";
      $this->sql_query['join'] = "$type JOIN ($other_sql) AS t1 ";
      $i = 0;
      foreach ($conditions as $key => $val) {
        if ($i == 0) {
          $join_conditions = "`t1`.`$key` = `$this->table_refs`.`$val`";
        } elseif ( count($conditions) > $i ) {
          $join_conditions .= " AND `t1`.`$key` = '$val'";
        }
        $i++;
      }
      $this->sql_query['join_clause'] = "ON ($join_conditions)";
    }
    /*
    $sql = "SELECT * FROM $this->table_refs $type JOIN $other_table";
    //if ( ! isset($this->sql_query['operation']) ) $this->select();
    $conditions = $this->join_clause($this->table_refs, $other_table, $predicate);
    $sql .= " ON $conditions";
    printf("SQL: %s<br/>\n", $sql);
    //$sth = $this->dbh->prepare($sql);
    //$sth->execute();
    //return $sth->fetchAll();
     */
    return $this;
  }

  private function join_clause ($table, $other_table, array $params) {
    print_r($params);
    /*
    $par1 = array_shift($params);
    $key = key($par1);
    $val = $par1[$key];
    $clause = "$other_table.$key = $table.$val";
    print_r($params);
     */
    $i = 0;
    $cnt = count($params);
    //foreach ($params as $param) {
      foreach ($params as $key => $val) {
        if ($i == 0) {
          $clause = "$other_table.$key = $table.$val";
        } elseif ( $cnt > $i ) {
          $clause .= " AND $other_table.$key = $val";
        }
        $i++;
      }
    //}
    //printf("W: %s <br/>\n", $clause);
    return $clause;
  }

  public function inner_join ($other_table, array $predicate) {
    return $this->join_table('INNER', $other_table, $predicate);
  }

  public function join ($other_table, array $predicate) {
    return $this->inner_join($other_table, $predicate);
  }

}

?>
