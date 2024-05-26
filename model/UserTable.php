<?php

//Modelo de la tabla user de la base de datos con todas las operaciones

class UserTable{

    private int $id;
    private string $uuid;
    private ?string $fullname;
    private string $email;
    private ?string $address;
    private ?string $birthdate;
    private string $created_at;
    private string $updated_at;

    public function __construct(
        int $id = 0,
        string $uuid = "",
        ?string $fullname = "",
        string $email = "",
        ?string $address = "",
        ?string $birthdate = "",
    )
    {
        $this->id = 0;
        $this->uuid = $uuid;
        $this->fullname = $fullname;
        $this->email = $email;
        $this->address = $address;
        $this->birthdate = $birthdate;

    }

    #region getters
    public function getId() : int { return $this->id; }
    public function getUuid() : string { return $this->uuid; }
    public function getFullname() : string | null { return $this->fullname; }
    public function getEmail() : string { return $this->email; }
    public function getAddress() : string | null { return $this->address; }
    public function getBirthdate() : string | null { return $this->birthdate; }
    public function getCreatedAt() : string { return $this->created_at; }
    public function getUpdatedAt() : string { return $this->updated_at; }
    #endregion

    #region setters
    public function setId(int $id) { $this->id = $id; }
    public function setUuid(string $uuid) { $this->uuid = $uuid; }
    public function setFullname(string $fullname) { $this->fullname = $fullname; }
    public function setEmail(string $email) { $this->email = $email; }
    public function setAddress(string $address) { $this->address = $address; }
    public function setBirthdate(string $birthdate) { $this->birthdate = $birthdate; }
    public function setCreatedAt(string $created_at) { $this->created_at = $created_at; }
    public function setUpdatedAt(string $updated_at) { $this->updated_at = $updated_at; }
    #endregion


    //método público para la carga de información según id o uuid
    public function get(PDO $pdo) : bool{
        $query = "SELECT id,uuid,fullname,email,address,birthdate,created_at, updated_at FROM users WHERE id = ? OR uuid = ? LIMIT 1";
        $statment = $pdo->prepare($query);
        $statment->bindParam(1, $this->id, PDO::PARAM_INT);
        $statment->bindParam(2, $this->uuid, PDO::PARAM_STR);
        if ($statment->execute()) {
            if($statment->rowCount() > 0){
                $data = $statment->fetch(PDO::FETCH_ASSOC);
                $this->id = $data["id"];
                $this->uuid = $data["uuid"];
                $this->fullname = $data["fullname"];
                $this->email = $data["email"];
                $this->address = $data["address"];
                $this->birthdate = $data["birthdate"];
                $this->created_at = $data["created_at"];
                $this->updated_at = $data["updated_at"];
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    //método público para el retorno de todos los usuarios
    public static function all(PDO $pdo) : array
    {
        $dataArray = array(); //array para almacenar todos los usuarios
        $query = "SELECT id,uuid,fullname,email,address,birthdate,created_at, updated_at FROM users ORDER BY id DESC";
        $statment = $pdo->prepare($query);
        if ($statment->execute()) {
            $data = $statment->fetchAll(PDO::FETCH_ASSOC);
            foreach ($data as $d){
                //almacenar usuario en el arreglo
                array_push($dataArray,array(
                    "id"=>$d["id"],
                    "uuid"=>$d["uuid"],
                    "fullname"=>$d["fullname"],
                    "email"=>$d["email"],
                    "address"=>$d["address"],
                    "birthdate"=>$d["birthdate"],
                    "created_at"=>$d["created_at"],
                    "updated_at"=>$d["updated_at"]
                ));
            }
        }
        return $dataArray;
    }

    //método público para la creación de nuevos usuarios
    public function create(PDO $pdo) : bool
    {
        $query = "INSERT INTO users (uuid,fullname,email,address,birthdate,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())";
        $statment = $pdo->prepare($query);
        $statment->bindParam(1, $this->uuid, PDO::PARAM_STR);
        $statment->bindParam(2, $this->fullname, PDO::PARAM_STR);
        $statment->bindParam(3, $this->email, PDO::PARAM_STR);
        $statment->bindParam(4, $this->address, PDO::PARAM_STR);
        $statment->bindParam(5, $this->birthdate, PDO::PARAM_STR);
        if ($statment->execute()) {
            return true;
        }
        return false;
    }

    //método público para la modificación de nuevos usuarios
    public function update(PDO $pdo, bool $updateEmail = false) : bool
    {
        $query = "UPDATE users SET fullname = ?,address = ?,birthdate = ?, updated_at = NOW() WHERE uuid = ?";
        $statment = $pdo->prepare($query);
        $statment->bindParam(1, $this->fullname, PDO::PARAM_STR);
        $statment->bindParam(2, $this->address, PDO::PARAM_STR);
        $statment->bindParam(3, $this->birthdate, PDO::PARAM_STR);
        $statment->bindParam(4, $this->uuid, PDO::PARAM_STR);
        if ($statment->execute()) {
            if($updateEmail){
                $query2 = "UPDATE users SET email = ?, updated_at = NOW() WHERE uuid = ?";
                $statment2 = $pdo->prepare($query2);
                $statment2->bindParam(1, $this->email, PDO::PARAM_STR);
                $statment2->bindParam(2, $this->uuid, PDO::PARAM_STR);
                if ($statment2->execute()) {
                    if ($statment->rowCount() < 0) return false;
                }else{
                    return false;
                }
            }
            return ($statment->rowCount() > 0);
        }
        return false;
    }

    //método público para el borrado de usuarios
    public function delete(PDO $pdo) : bool
    {
        $query = "DELETE FROM users WHERE uuid = ?";
        $statment = $pdo->prepare($query);
        $statment->bindParam(1, $this->uuid, PDO::PARAM_STR);
        if ($statment->execute()) {
            return ($statment->rowCount() > 0);
        }
        return false;
    }

    //metodo público para validar usuarios ingresados
    public function validateUser(PDO $pdo) : bool
    {
        if($this->email != null && trim($this->email) != ""){
            $query = "SELECT id FROM users WHERE email = ?";
            $statment = $pdo->prepare($query);
            $statment->bindParam(1, $this->email, PDO::PARAM_STR);
            if ($statment->execute()) {
                $statment->fetchAll(PDO::FETCH_ASSOC);
                return ($statment->rowCount() <= 0);
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}