<?php namespace Models;

/**
 * MITDone PHP MVC Framework 2018 
 *
 * @author      MITDone llc <dev@mitdone.com>
 * @copyright   2018 MITDone llc
 * @license     MIT
 *
 * @link        https://mitdone.com
 *
 */
use MITDone\App\Model;
use MITDone\Http\FileUpload;
use MITDone;

class UsersModel extends Model
{

    /**
     * get all active users method
     * 
     * @return object
     */
    public function all()
    {   
        return $this->select->from('users', '*', ["deleted | = " => "0"]);
    }

    /**
     * count number of all active users
     *
     * @return int
     */
    public function countAllUsers()
    {
        return $this->select->countRows('users', 'WHERE deleted = 0');
    }

    /**
     * count users of last seven days
     *
     * @return   int
     */
    public function countLastSevenDaysUsers()
    {
        return $this->select->countLast7daysRows('users', 'AND deleted = 0');
    }

    /**
     * count users that are engineers.
     * 
     * @return int
     */
    public function countEngineers()
    {
       return $this->select->countRows('users', "WHERE type = 'engineer'");
    }

    /**
     * count new engineers
     * 
     * @return int
     */
    public function countLastSevenDaysEngineers()
    {
        return $this->select->countLast7daysRows('users', "AND type = 'engineer'");
    }

    /**
     * get user by id
     * 
     * @param  int id
     * @return object
     */
    public function getById($id)
    {
        return $this->select->from('users','*', [
            "id      | = " => $id . '| and',
            "deleted | = " => "0"
        ]);
    } 

    /**
     * delete user by id
     * 
     * @param  int id 
     * @return bool
     */
    public function deleteById($id)
    {
        if($this->getById($id)[0]) // foound ! 
        {
            if($this->update->set('users',["deleted" => "1"], ["id | =" => $id])) return 1;
            return 0;
        }

        return 0;
    }

    public function editUser($id = 0)
    {
        $user = $this->getById($id)[0];

        if($user):

            if(post("update"))
            {
                $data = [];
                
                if(!validate()->string(post('name')))        $data['errors'][] = "wrong name";
                if(!validate()->string(post('username')))    $data['errors'][] = "wrong username";
                if(!validate()->string(post('phone')))       $data['errors'][] = "wrong phone";
                if(!empty(post('adress')))
                {
                    if(!validate()->string(post('address')))     $data['errors'][] = "wrong address";
                }
                if(!validate()->int(post('role')))           $data['errors'][] = "wrong role";
                

                $name     = validate()->string(post('name'));
                $username = validate()->string(post('username'));
                $phone    = validate()->string(post('phone'));
                $adress   = validate()->string(post('address'));
                $role     = validate()->string(post('role'));

                if(empty($data['errors']))
                {
                    if(empty($_FILES['image']['name'])) // no image update
                    {
                        $update = $this->update->set('users',[ 
                            "name"=> $name,
                            "username"=> $username,
                            "phone"=> $phone,
                            "Address"=> $adress,
                            "role_id"=> $role
                        ], ["id | = "=> $id]);

                        return $update;
                    }else{

                        $up = upload();
                        $up->setUploadFile('image')->validate();
        
                        if(empty($up->errors()))
                        {
                            $image = $up->name .'.'.$up->ext;
            
                            $this->update->set('users',[ 
                                "name"=> $name,
                                "username"=> $username,
                                "phone"=> $phone,
                                "Address"=> $adress,
                                "role_id"=> $role,
                                "image"  => $image    
                
                            ], ["id | = "=> $id]);
            
                            $up->uploadFile(PHP_UP_IMG); // upload image

                            $up->delete(PHP_UP_IMG . $user->image); // delete old image
            
                            $resizer = imageResizer(PHP_UP_IMG . $image); // resize image
                            $resizer->resizeToBestFit(300, 300);
                            $resizer->save(PHP_UP_IMG . $image);

                            return 1;
                        }

                        return $up->errors();
                    }

                    return $data['errors'];

                }  
            }
        endif;
    }

    public function notify($id = 0)
    {
        if(post('notify'))
        {
           $user = new MITDone\Api;
        }
    }

    public function message($id = 0)
    {

    }
}