<?php if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script');

class Tokens{

    Public function deftoken($id){
        $data = array();

        switch ($id) {
            case 1:
                //Token ccapa
                $data['ruta']  = "https://www.pse.pe/api/v1/782529ec6e184f9faf631b905df687ba6dae035978a643d1912dee909929a80e";
                $data['token'] = "eyJhbGciOiJIUzI1NiJ9.Ijk1Y2E1ZDEwY2I3YjQ1ODFhY2FlMGY1NzE5NTkxMmI2OWZiNTM4NGUwOGZmNDVkYmJmYTI0YmY4YjAyYTA5YzMi.SsNS80SG3XRAkCSqgeksrLQVRYgdhG4rPiPiDG6cwUU";
                
                //Token cochera
                //$data['ruta'] = "https://www.pse.pe/api/v1/b2205019197f44ff8d92956791bd41f15eee4f68999345b793d8d71287e32eb8";
                //$data['token'] = "eyJhbGciOiJIUzI1NiJ9.IjBmMGI4NjY1MDBiMDQwYmNiMDk5YmRiZWYzNGQ3NGUxZTEwMjEzZjVlNzI3NDI3ZmI0NTVjODNkNGNmZTM4ZGQi.0ANj3uRAA34pooRrIWblEQb5OeXiKKHAvjVdCwXtqq4";
            break;

            default:
                //Token Ccapa
                $data['ruta']  = "https://www.pse.pe/api/v1/782529ec6e184f9faf631b905df687ba6dae035978a643d1912dee909929a80e";
                $data['token'] = "eyJhbGciOiJIUzI1NiJ9.Ijk1Y2E1ZDEwY2I3YjQ1ODFhY2FlMGY1NzE5NTkxMmI2OWZiNTM4NGUwOGZmNDVkYmJmYTI0YmY4YjAyYTA5YzMi.SsNS80SG3XRAkCSqgeksrLQVRYgdhG4rPiPiDG6cwUU";                
                
                //Token cochera
                //$data['ruta'] = "https://www.pse.pe/api/v1/b2205019197f44ff8d92956791bd41f15eee4f68999345b793d8d71287e32eb8";
                //$data['token'] = "eyJhbGciOiJIUzI1NiJ9.IjM4ZjMzZDM2Y2RmZjQxZDA4OTlkNGU2YjcyNDc1OTYzOGVmOWFhNGQxNTkyNDIzNGJlMGZlZmU3N2M0OGQyMmIi.c0vegErBqLH6NQBu_XyH9hSQI5VqxOmtxZLLOaEwlEE"; 
            break;
        }

        return $data;
    }
}
?>