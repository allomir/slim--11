<?php

namespace App;

class UserRepository {
    public function save(array $arrdataUser): ?int
    {
        $arrdataUsers = $this->getAll();
        $arrdataUser = array_merge(['id' => count($arrdataUsers) + 1], $arrdataUser);
        $newArrdata = array_merge($arrdataUsers, [$arrdataUser]);

        return file_put_contents(__DIR__ . '/users_data.txt', json_encode($newArrdata));
    }

    public function getAll(): array
    {
        $data = file_get_contents(__DIR__ . '/users_data.txt');
        if (!$data) {
            return [];
        }

        $arrdata = json_decode($data, true);

        return $arrdata === null ? [] : $arrdata ;
    }

    public function findByEmail(string $email): array
    {
        $users = $this->getAll();
        $user = array_values(array_filter($users, function ($user) use ($email) {
            return $user['email'] === $email;
        }));

        return $user[0] ?? [];
    }

    public function destroy(int $id): ?int
    {
        $arrdataUsers = $this->getAll();

        $arrDataNew =array_filter($arrdataUsers, function ($user) use ($id) {
            return $user['id'] !== $id;
        });

        return file_put_contents(__DIR__ . '/users_data.txt', json_encode($arrDataNew));
    }
}