<?php 

use \ApiTester;
use Faker\Factory as Faker;

class UserTestCept
{
   
    protected $path = '/user';


    public function _before(){}

    public function _after(){}

    
    //Create users
    public function createUser(ApiTester $I)
    {

        $faker = Faker::create();
        $I->haveHttpHeader('Content-Type', 'application/json');

        for($i=0; $i<=10; $i++)
        {
            
            $first_name = $faker->firstName();
            $last_name = $faker->lasName();
            $email = $faker->email();
            $password = $faker->password();
            $public = rand(0,1);

            $I->sendPOST($this->path, array(
                'first_name' => $first_name, 
                'last_name' => $last_name,
                'email' => $email,
                'password' => $password,
                'public' => $public
                ));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['first_name' => $first_name, 
                                        'last_name' => $last_name,
                                        'email' => $email,
                                        'password' => $password]);
        }

    }



    //Edit users
    public function editUser(ApiTester $I, $id)
    {
        $faker = Faker::create();
        $I->haveHttpHeader('Content-Type', 'application/json');

        for($i=0; $i<=5; $i++)
        {
            $first_name = $faker->firstName();
            $middle_name = $faker->firstName();
            $last_name = $faker->lasName();
            $public = rand(0,1);

            $I->sendPATCH($this->path, array(
                'id' => $id,
                'first_name' => $first_name, 
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'public' => $public
                ));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['first_name' => $first_name,
                                        'middle_name' => $middle_name, 
                                        'last_name' => $last_name,
                                        'password' => $password]);
        }

    }

    //Edit email
    public function editEmail(ApiTester $I)
    {
        $faker = Faker::create();
        $I->haveHttpHeader('Content-Type', 'application/json');

        for($i=0; $i<=5; $i++)
        {
            $email = $faker->email();

            $I->sendPATCH($this->path, array(
                'id' => $id,
                'email' => $email));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['email' => $email]);
        }
    }


    //Edit Ethnicity
    public function editEthnicity(ApiTester $I, $id)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $ethnic_origin = rand(1,22);

        $I->sendPATCH($this->path, array(
            'id' => $id,
            'ethnic_origin_id' => $ethnic_origin));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson(['ethnic_origin_id' => $ethnic_origin]);
    }


    //Edit Birthday
    public function editBirthday(ApiTester $I, $id)
    {
        $faker = Faker::create();
        $I->haveHttpHeader('Content-Type', 'application/json');

        for($i=0; $i<=5; $i++)
        {
            $birth = $faker->date('Y-m-d');

            $I->sendPATCH($this->path, array(
                'id' => $id,
                'date_of_birth' => $birth));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['date_of_birth' => $birth]);
        }
    }



    //Edit Password
    public function editPassword(ApiTester $I, $id)
    {
        $faker = Faker::create();
        $I->haveHttpHeader('Content-Type', 'application/json');

        for($i=0; $i<=5; $i++)
        {
            $password = $faker->password();

            $I->sendPATCH($this->path, array(
                'id' => $id,
                'password' => $password));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['password' => $password]);
        }

    }

    
    //Delete user
    public function deleteUser(ApiTester $I, $id)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        $I->sendDELETE($this->path, array('id' => $id));
        $I->seeResponseCodeIs(200);
        $I->dontSeeRecord('users', ['id' => $id]);
    }


    //Assign Permissions: the rol has not permissions yet
    public function assignPermissions(ApiTester $I, $role_id)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        //Create array with permissions
        $permissions = array();
        $j = 0;
        for($i=0;$i<=20;$i++)
        {
            $permission = rand(1,21);
            $permissions[$j] = $permission;
        }
        //Remove duplicate values
        $permissions = array_unique($permissions);

        //Insert permissions
        for($i=0; $i<count($permissions); $i++)
        {
            $I->sendPOST($this->path, array(
                'role_id' => $role_id,
                'permission_id' => $permissions[$i]));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['permission' => $permissions[$i]]);
        }
    }


    //Assign Permisions (permissions received in the function's parameters)
    //When the role has already permissions
    public function assignUpadatePermission(ApiTester $I, $role_id, $permissions)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

       
        //Delete permissions that the rol had 
        $I->sendDELETE($this->path, array('role_id' => $role_id));
        $I->seeResponseCodeIs(200);
        $I->dontSeeRecord('users', ['role_id' => $role_id]);

        //Insert new permissions
        for($i=0; $i<count($permissions);$i++)
        {
            $I->sendPOST($this->path, array(
                'role_id' => $role_id,
                'permission_id' => $permissions[$i]));
            $I->seeResponseCodeIs(200);
            $I->seeResponseIsJson();
            $I->seeResponseContainsJson(['permission_id' => $permission_id]);
        }
    }
    
    //Security: no voting without permission
    public function userCanVote(ApiTester $I, $id)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');

        //Check user: the user must be verified (have goverment id)
        $I->sendGET($this->path, array('id' => $id));
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        if($I->seeResponseContainsJson(['identity_verified' => 1]))
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    
    //Security: no voting without permission
    public function userCanVote2(ApiTester $I, $id)
    {
        
        if($I->seeInDataBase('users', array('id' => $id, 'identity_verified' => 1)))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}

?>
