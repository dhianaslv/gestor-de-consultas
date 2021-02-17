<?php

namespace src\data\repository;

use src\data\repository\Connection;
use app\models\MedicalAppointment;

class MedicalAppointmentRepository
{

    private $conn;

    public function __construct()
    {
        $this->conn = new Connection();
    }

    public function makeAnAppointment($patient_cpf, $genre, $specialty, $date, $time, $room)
    {
        try {

            $select = "SELECT id FROM doctor WHERE genre = :genre AND specialty = :specialty AND active = :active";

            $stmt = $this->conn->getConnection()->prepare($select);

            $stmt->execute(array(
                ':genre' => $genre,
                ':specialty' => $specialty,
                ':active' => 1,
            ));

            $doctor = $stmt->fetchAll();

            if ($doctor != null) {
                $id_doctor = $doctor[0]['id'];

                $sql = "INSERT INTO medical_appointment (cpf_patient_fk, id_doctor_fk, 
                        id_room_fk, time, date) 
                        VALUES (:cpf_patient_fk, :id_doctor_fk,:id_room_fk, :time, :date)";


                $stmt = $this->conn->getConnection()->prepare($sql);

                $success = $stmt->execute(array(
                    ':cpf_patient_fk' => $patient_cpf,
                    ':id_doctor_fk' => $id_doctor,
                    ':id_room_fk' => $room,
                    ':time' => $time,
                    ':date' => $date,
                ));

                if ($success) {
                    return $success;
                }

                $response = "Não foi possível marcar a consulta. Tente mais tarde.";

                return $response;
            }

            return "Não há nenhum médico com essa descrição, por isso não foi possível marcar a consulta.";
        } catch (\Exception $e) {
            return "Exception: $e";
        } finally {
            $this->conn->disconnect();
        }
    }

    public function allMedicalAppointments()
    {
        try {
            $sql = "SELECT MA.id, P.full_name, D.name, MA.time, 
                    MA.date, MA.arrival_time, MA.id_room_fk, R.type, MA.realized 
                    FROM medical_appointment AS MA
                        INNER JOIN patient AS P 
                            ON (MA.cpf_patient_fk = P.cpf)
                        INNER JOIN doctor AS D 
                            ON (MA.id_doctor_fk = D.id)
                        INNER JOIN room AS R
                            ON (MA.id_room_fk = R.id)
                    WHERE MA.realized = :realized
                        ORDER BY MA.realized != 0, MA.arrival_time IS NULL, MA.arrival_time ASC";

            $stmt = $this->conn->getConnection()->prepare($sql);

            $stmt->execute(array(
                ':realized' => 0,
            ));

            $result = $stmt->fetchAll();

            if ($result != null) {
                $list = [];

                foreach ($result as $row) {
                    $id = $row['id'];
                    $patient = $row['full_name'];
                    $doctor = $row['name'];
                    $time = $row['time'];
                    $date = $row['date'];
                    $type = $row['type'];
                    $id_room = $row['id_room_fk'];

                    if ($row['realized']) {
                        $realized = "Sim";
                    } else {
                        $realized = "Não";
                    }

                    if ($row['arrival_time'] != null) {
                        $arrival_time = $row['arrival_time'];
                    } else {
                        $arrival_time = "-----";
                    }

                    $medical_appointment = new MedicalAppointment(
                        $id,
                        $patient,
                        $doctor,
                        [$id_room, $type],
                        $date,
                        $time,
                        $arrival_time,
                        $realized
                    );

                    array_push($list, $medical_appointment);
                }

                return $list;
            }

            $response = "Não foi possível trazer a lista de consultas";

            return $response;
        } catch (\Exception $e) {

            return "Exception: $e";
        } finally {
            $this->conn->disconnect();
        }
    }

    public function fetchMedicalAppointment($id)
    {
        try {
            $sql = "SELECT D.specialty, D.genre, D.name, MA.id, 
                    MA.realized, MA.date, MA.cpf_patient_fk ,MA.time, 
                    MA.arrival_time, MA.id_room_fk, R.type
                    FROM medical_appointment AS MA
                        INNER JOIN doctor AS D 
                            ON (MA.id_doctor_fk = D.id)
                        INNER JOIN room AS R
                            ON (MA.id_room_fk = R.id)
                    WHERE MA.id = :id
                    GROUP BY D.specialty, D.genre, D.name, MA.id, 
                        MA.realized, MA.date, MA.cpf_patient_fk ,
                        MA.time, MA.arrival_time";

            $stmt = $this->conn->getConnection()->prepare($sql);

            $stmt->execute(array(
                ':id' => $id,
            ));

            $result = $stmt->fetchAll();

            if ($result != null) {
                $name = $result[0]['name'];
                $genre = $result[0]['genre'];
                $specialty = $result[0]['specialty'];
                $realized = $result[0]['realized'];
                $date = $result[0]['date'];
                $time = $result[0]['time'];
                $patient_cpf = $result[0]['cpf_patient_fk'];
                $type = $result[0]['type'];
                $id_room = $result[0]['id_room_fk'];

                if ($result[0]['arrival_time'] != null) {
                    $arrival_time = $result[0]['arrival_time'];
                } else {
                    $arrival_time = "-----";
                }

                $medical_appointment = new MedicalAppointment(
                    $id,
                    $patient_cpf,
                    [$name, $specialty, $genre],
                    [$id_room, $type],
                    $date,
                    $time,
                    $arrival_time,
                    $realized
                );

                return $medical_appointment;
            }

            $response = "Não foi possível trazer o médico(a) escolhido.";
            return $response;
        } catch (\Exception $e) {

            return "Exception: $e";
        } finally {
            $this->conn->disconnect();
        }
    }

    public function update($medical_appointment)
    {
        try {

            $select = "SELECT id FROM doctor WHERE genre = :genre AND specialty = :specialty AND active = :active";

            $stmt = $this->conn->getConnection()->prepare($select);

            $stmt->execute(array(
                ':specialty' => $medical_appointment->getIdDoctor()[0],
                ':genre' => $medical_appointment->getIdDoctor()[1],
                ':active' => 1,
            ));

            $doctor = $stmt->fetchAll();

            if ($doctor != null) {
                $id_doctor = $doctor[0]['id'];

                $sql = "UPDATE medical_appointment SET cpf_patient_fk = :cpf_patient_fk,
                id_doctor_fk = :id_doctor_fk, id_room_fk = :id_room_fk, time = :time, 
                date = :date, arrival_time = :arrival_time, realized = :realized 
                WHERE id = :id";

                $stmt = $this->conn->getConnection()->prepare($sql);

                $success = $stmt->execute(array(
                    ':id' => $medical_appointment->getId(),
                    ':cpf_patient_fk' => $medical_appointment->getPatientCpf(),
                    ':id_doctor_fk' => $id_doctor,
                    ':id_room_fk' => $medical_appointment->getIdRoom(),
                    ':time' => $medical_appointment->getTime(),
                    ':date' => $medical_appointment->getDate(),
                    ':arrival_time' => $medical_appointment->getArrivalTime(),
                    ':realized' => $medical_appointment->getRealized(),
                ));

                if ($success) {
                    return $success;
                }

                $response = "Não foi possível realizar as alterações desejadas na consulta. Tente mais tarde";

                return $response;
            }

            return "Não há nenhum médico com essa descrição, por isso não foi possível realizar a alteração.";
        } catch (\Exception $e) {
            return "Exception: $e";
        } finally {
            $this->conn->disconnect();
        }
    }
}
