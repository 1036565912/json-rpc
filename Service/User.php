<?php
declare(strict_types=1);
namespace Service;

use Model\User as UserModel;
class User
{
    /** @var UserModel */
    protected $model;
    public function __construct(UserModel $user)
    {
        $this->model = $user;
    }

    /**
     * 获取用户信息
     * @param string $username
     * @param int $age
     * @return array
     * @author chenlin
     * @date 2020/10/19
     */
    public function getInfo(string $username, int $age): array
    {
        if (!isset($this->model->name_dict[$username])) {
            return [
                'name' => 'not exist name',
                'age'  => 'unknown',
                'hometown' => 'unknown'
            ];
        }

        return [
            'name' => $this->model->name_dict[$username]['name'],
            'age' => $age,
            'hometown' => $this->model->name_dict[$username]['hometown'],
        ];
    }
}