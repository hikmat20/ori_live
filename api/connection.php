<?php

	class database_ORI extends mysqli {
		private $DB_HOST 		= 'localhost';
		private $DB_DATABASE 	= 'sentralsistem';
        private $DB_USER 		= 'sentralsistem';
        private $DB_PASSWORD 	= '$$sentral$$99oo';



		public function __construct() {
			$this->_conn = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD);

			if(!$this->_conn) {
				echo 'Connection failed!<br>';
			}
		}

		public function connect() {
			if(!mysqli_select_db($this->_conn, $this->DB_DATABASE)) {
				die("Cannot connect database..<br>");
			}

			return $this->_conn;

		}
	}



?>
