<?php

namespace App;

class UserRepositoryCoo {
    public function save(array $arrdataUser): bool
    {
        $arrdataUsers = $this->getAll();
        $arrdataUser = array_merge(['id' => count($arrdataUsers) + 1], $arrdataUser);
        $newArrdata = array_merge($arrdataUsers, [$arrdataUser]);

        return setcookie('user_repo', json_encode($newArrdata));
    }

    public function getAll(): array
    {
        return json_decode($_COOKIE['user_repo'] ?? json_encode([]), true);
    }

    public function destroy(int $id): ?int
    {
        $arrdataUsers = $this->getAll();

        $arrDataNew =array_filter($arrdataUsers, function ($user) use ($id) {
            return $user['id'] !== $id;
        } );

        return file_put_contents(__DIR__ . '/users_data.txt', json_encode($arrDataNew));
    }
}