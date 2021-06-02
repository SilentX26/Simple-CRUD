<?php
/*
    @ Library Simple CRUD
    @ Dibuat dengan penuh cinta oleh Muhammad Randika Rosyid :)
    @ Dibuat untuk mempermudah para developer untuk mengembangkan aplikasinya, terlebih untuk para developer yang masih stay sama native :D
    @ Library ini bebas untuk digunakan oleh siapapun, tetapi dengan tanpa mengurangi rasa hormat kepada pembuat library ini. :)
    @ Seluruh hak cipta dilindungi oleh undang-undang ( UU Nomor 28 tahun 2014 )
	@ Kontak saya: randika.rosyid2@gmail.com (email)  
*/

class Simple_CRUD
{
    public $db;
    protected $config;

    /*
        # Constructor, fungsi yg pertama kali dipanggil dalam class.
        # Yang akan memulai koneksi ke database, sesuai dengan yang digunakan dalam konfigurasi.
    */
    function __construct($config)
    {
        $this->config = (object) $config;
        $this->db = (isset($this->config->host, $this->config->user, $this->config->pass, $this->config->db) !== FALSE)
            ? mysqli_connect($this->config->host, $this->config->user, $this->config->pass, $this->config->db)
            : FALSE;
    }

    /*
        # Fungsi yang akan menangani jika koneksi ke database gagal.
    */
    private function _unconnect()
    {
        $error_message = mysqli_connect_error();
        error_log(":[SIMPLE CRUD]: Koneksi ke database gagal, pesan error: {$error_message}");
        return FALSE;
    }

    /*
        # Fungsi yang akan memberi notif error ke log server jika query yang dijalankan gagal.
        # Fungsi ini hanya akan dipanggil jika anda menambahkan parameter is_debug di konfigurasi (saat inisialisasi class).
    */
    private function _error_query()
    {
        $error_message = $this->db->error;
        error_log(":[SIMPLE CRUD]: Query gagal di eksekusi, pesan error: {$error_message}");
    }

    /*
        # Fungsi yang membantu untuk menghasilkan string query SQL yang digunakan untuk fungsi insert.
    */
    private function _insert_value($values)
    {
        return "('" .implode("', '", array_values($values)). "')";
    }

    private function _update_value($values)
    {
        $result = '';

        foreach($values as $key => $value) {
            $is_not_escape = (isset($value['escape']) && $value['escape'] === FALSE);
            $value = (is_array($value)) ? $value['value'] : $value;

            $result .= ($is_not_escape !== FALSE) ? "{$key} = {$value}" : "{$key} = '{$value}'";
        }

        return $result;
    }

    /*
        # Fungsi yang akan menghasillkan string untuk pengkondisian where di SQL.
    */
    private function _where($condition)
    {
        if(is_array($condition)) {
            $result = '';
            $i = 0;
            foreach($condition as $key => $value) {
                $is_not_escape = (isset($value['escape']) && $value['escape'] === FALSE);
                $value = (is_array($value)) ? $value['value'] : $value;

                if(preg_match('/^[&&||!=]/', $key) != 0) {
                    $logic = preg_match('/^[&&||]+/', $key, $array_logic);
                    $logic = $array_logic[0];
                } else {
                    $logic = '&&';
                }

                if(preg_match('/[><>=<=!=]$/', $key) != 0) {
                    $opr = preg_match('/[><>=<=!=]+$/', $key, $array_opr);
                    $opr = $array_opr[0];
                } else {
                    $opr = '=';
                }

                $key = preg_replace('/[^a-zA-Z0-9]+/m', '', $key);
                $logic = ($i != 0) ? $logic : '';
                $result .= ($is_not_escape !== FALSE) ? " {$logic} {$key} {$opr} {$value}" : " {$logic} {$key} {$opr} '{$value}'";

                $i++;
            }

            return $result;

        } else {
            return $condition;
        }
    }

    /*
        # Fungsi yang sangat penting, fungsi ini yang akan meng-handle perintah join, where dll.
        # Lalu akan memanggil fungsi-fungsi kecil diatas untuk melaksanakan perintah sesuai dengan parameter yang diminta.
    */
    private function _fetch_query($param = [])
    {
        $result = '';

        if(isset($param['join'])) {
            if(isset($param['join'][0])) {
                foreach($param['join'][0] as $key => $value) {
                    $on = (isset($value['on']))
                        ? 'ON ' . $this->_where($value['on'])
                        : '';
                    $result .= " {$value['param']} JOIN {$value['table']} {$on}";
                }

            } else {
                $on = (isset($param['join']['on']))
                    ? 'ON ' . $this->_where($param['join']['on'])
                    : '';
                $result .= " {$param['join']['param']} JOIN {$param['join']['table']} {$on}";
            }
        }

        if(isset($param['where']))
            $result .= ' WHERE ' . $this->_where($param['where']);

        if(isset($param['group_by']))
            $result .= " GROUP BY {$param['group_by']}";
        
        if(isset($param['order_by'])) {
            $type = (isset($param['order_by']['type']))
                ? strtoupper($param['order_by']['type'])
                : '';
            $result .= " ORDER BY {$param['order_by']['column']} {$type}";
        }

        if(isset($param['limit']))
            $result .= " LIMIT {$param['limit']}";
        
        if(isset($param['offset']))
            $result .= " OFFSET {$param['offset']}";

        return $result;
    }

    /*
        # Fungsi yang memiliki fungsi untuk "membenarkan" data yang di minta sebelum di return.
        # Contoh saja jika anda memanggil fungsi get_rows dengan parameter array (di parameter return), maka fungsi ini yang akan meng-handle permintaan tersebut.
    */
    private function _fetch_result($mysqli_obj, $return, $is_indexed = FALSE)
    {
        if($mysqli_obj === FALSE)
            return FALSE;

        if($is_indexed === TRUE) {
            $result = [];
            $x = ($return == 'object') ? 'fetch_object' : 'fetch_assoc';

            while($temp_result = $mysqli_obj->$x())
                $result[] = $temp_result;
            return ($return == 'object')
                ? (object) $result
                : $result;

        } else {
            return ($return == 'object')
                ? $mysqli_obj->fetch_object()
                : $mysqli_obj->fetch_assoc();
        }
    }

    /*
        # Fungsi publik untuk menjalankan query.
        # Parameter:
            - [query] => String query SQL yang ingin dijalankan.
    */
    function query($query)
    {
        if(!$this->db)
            return $this->_unconnect();

        $exec_query = $this->db->query($query);
        if($this->db->errno != 0 && isset($this->config->is_debug) && $this->config->is_debug === TRUE)
            $this->_error_query();

        return $exec_query;
    }

    /*
        # Fungsi publik yang berfungsi untuk mendapatkan/membaca data.
        # Parameter:
            - [table] => Nama tabel yang diinginkan
            - [param] : 
                - [select] => Nama field yang ingin diambil, defaulnya library ini akan mengambil seluruh data.
                - [join] => Melakukan perintah join ke tabel lain, silahkan lihat di file sample untuk melihat struktur array pada perintah join
                - [order_by] => Menyortir data yang diambil sesuai dengan field dan tipe sortir yang diberikan
                - [group_by] => Melakukan grouping pada data yang diambil
                - [limit] => Membatasi data yang akan diambil
                - [offset] => mengambil data dari titik offset yang sudah diberikan
                - [return] => Return tipe yang diinginkan, dapat berupa object maupun array.
                - [is_indexed] => Gunakan TRUE jika ingin mengambil lebih dari 1 data, perlu diingat, harap gunakan boolean (TRUE/FALSE), karena disini menggunakan pembanding yang akan membandingkan tipe data juga.
    */
    function get_rows($table, $param = [])
    {
        $query = (isset($param['select'])) ? "SELECT {$param['select']}" : 'SELECT *';
        $query .= " FROM {$table}";
        $query .= $this->_fetch_query($param);

        $return = (isset($param['return'])) ? $param['return'] : 'object';
        $is_indexed = (isset($param['is_indexed'])) ? $param['is_indexed'] : FALSE;

        $exec_query = $this->query($query);
        return $this->_fetch_result($exec_query, $return, $is_indexed);
    } 

    /*
        # Fungsi publik yang berfungsi untuk menghitung data.
        # Parameter:
            - [table] => Nama tabel yang diinginkan
            - [param] :
                - [join] => Melakukan perintah join ke tabel lain, silahkan lihat di file sample untuk melihat struktur array pada perintah join
                - [order_by] => Menyortir data yang diambil sesuai dengan field dan tipe sortir yang diberikan
                - [index] => Primary key/index tabel yang digunakan untuk memilih data dari tabel, digunakannya metode ini agar query yang dijalankan tidak membebani server (semisal yang di select adalah seluruh data dari tabel).
    */
    function count_rows($table, $param = [])
    {
        $index = (isset($param['index'])) ? $param['index'] : 'id';
        
        $query = "SELECT {$index} FROM {$table}";
        $query .= $this->_fetch_query($param);

        $exec_query = $this->query($query);
        return $exec_query->num_rows;
    }

    /*
        # Fungsi publik yang berfungsi untuk menghapus seluruh data dari tabel yang diinginkan.
        # Parameter:
            - [table] => Nama tabel yang diinginkan
    */
    function truncate($table)
    {
        return $this->query("TRUNCATE TABLE {$table}");
    }

    /*
        # Fungsi publik yang berfungsi untuk menghapus data dari tabel yang diinginkan.
        # Parameter:
            - [table] => Nama tabel yang diinginkan
            - [where] => Kondisi yang diinginkan, dapat berbentuk array asosiatif maupun string.
    */
    function delete($table, $where = [])
    {
        $query = "DELETE FROM {$table}";
        $query .= (!empty($where)) ? ' WHERE ' . $this->_where($where) : '';

        return $this->query($query);
    }

    /*
        # Fungsi publik yang berfungsi untuk menambahkan data ke tabel yang diinginkan.
        # Parameter:
            - [table] => Nama tabel yang diinginkan
            - [data] => Array data yang akan ditambahkan.
    */
    function insert($table, $data)
    {
        $query = "INSERT INTO {$table} ";
        if(isset($data[0]) && is_array($data[0])) {
            $i = 0;
            foreach($data as $key => $value) {
                if($i == 0)
                    $query .= ' (' .implode(', ', array_keys($value)). ') VALUES ';

                $comma = ($key != 0) ? ',' : '';
                $query.= "{$comma} " . $this->_insert_value($value);

                $i++;
            }

        } else {
            $query .= ' (' .implode(', ', array_keys($data)). ') VALUES ';
            $query .= $this->_insert_value($data);
        }

        return $this->query($query);
    }

    /*
        # Fungsi publik yang berfungsi untuk mengubah data dari tabel yang diinginkan.
        # Parameter:
            - [table] => Nama tabel yang diinginkan
            - [data] => Array data yang akan diubah.
            - [where] => Kondisi yang diinginkan, dapat berbentuk array asosiatif maupun string.
    */
    function update($table, $data, $where = [])
    {
        $string_data = $this->_update_value($data);
        $query = "UPDATE {$table} SET {$string_data}";
        $query .= (!empty($where)) ? ' WHERE ' . $this->_where($where) : '';
        
        return $this->query($query);
    }
}