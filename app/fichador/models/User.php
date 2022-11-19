<?php

namespace app\fichador\models;

use \app\fichador\core\{Conn, BaseClass, Confiles};
use PHPMailer\PHPMailer\{PHPMailer, Exception, SMTP};

class User extends BaseClass
{
    private
        $id,
        $dni,
        $name,
        $email,
        $pass,
        $event;

    function __construct(string $name_user)
    {
        $this->name = $name_user;
        $this->load_user();
    }
    private function load_user(): self
    {
        $conn = new Conn('singin', 'employee', self::LOGON);
        $data = $conn->getBy("name LIKE '" . $this->name . "'")->get();
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }
    public function load_record(string $action) : self
    {
        $action_name  = $action == 'singin' ? 'Entrada' : 'Salida';
        $hash = hash('sha256', $this->id  . $action . date('U'));
        $date = date('Y-m-d H:i:s'); 
        $action = $action == 'singin' ? 1 : 0;
        $this->event = ['action'=>$action, 'action_name'=>$action_name, 'date'=>$date, 'hash'=>$hash ];
        
        return $this;
    }
    public function save_record(): self
    {
        $conn = new Conn('singin', 'sings', self::LOGON);
        [$action, $an, $date, $hash] = $this->event;
        $conn->insert(['id_employee'=>$this->id, 'action'=>$action, 'date_time'=>$date, 'hash' => $hash]);
        $this->return = $conn->return['success'] ? $hash : null;

        return $this;
    }

    public function send_email(): self
    {
        $credentials = Confiles::get_env(self::FOLDER_ROOT());
        $mail = new PHPMailer(true);
        // Configuración para mandar emails
        
        //Server settings
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->SMTPDebug  = self::LOGON;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = $credentials['EMAIL_HOST'];                       // Specify main and backup SMTP servers
        $mail->Username   = $credentials['EMAIL_USER'];                           // SMTP username
        $mail->Password   = $credentials['EMAIL_PASS'];                               // SMTP password
        $mail->SMTPSecure = $credentials['EMAIL_SECURE'];                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = $credentials['EMAIL_PORT'];                                    // TCP port to connect to
        $mail->From       = $credentials['EMAIL_USER'];
        $mail->FromName   = $credentials['EMAIL_FROM'];
        $mail->SetFrom($credentials['EMAIL_USER']);
        
        //config 
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);  

        //definimos el destinatario (dirección y, opcionalmente, nombre)
        $mail->AddAddress($this->email, $this->name);
        //Definimos el tema del email
        $mail->Subject = 'Registro horario';
        $mail->Body = 'Se ha registrado en su jornada laboral un evento: \n de ' . $this->event['action'] .  '\n el ' . $this->event['date'] . ' \n con hash ' . $this->event['hash'];
        $mail->AltBody =  'Se ha registrado en su jornada laboral un evento: de ' . $this->event['action'] .  ' el ' . $this->event['date'] . ' con hash ' . $this->event['hash'];;

        //Y por si nos bloquean el contenido HTML (algunos correos lo hacen por seguridad) una versión alternativa en texto plano 
        // (también será válida para lectores de pantalla)
        $this->return = $mail->Send();
        
        return $this;
    }
    public function id()
    {
        return $this->id;
    }
    public function dni(string $value = null)
    {
        $name_fun = explode('::', __METHOD__)[1];
        if ($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function user(string $value = null)
    {
        $name_fun = explode('::', __METHOD__)[1];
        if ($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function email(string $value = null)
    {
        $name_fun = explode('::', __METHOD__)[1];
        if ($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
    public function pass(string $value = null)
    {
        $name_fun = explode('::', __METHOD__)[1];
        if ($value) $this->{$name_fun} = $value;
        return $this->{$name_fun};
    }
}
