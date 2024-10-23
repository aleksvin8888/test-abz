<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class UserService
{
    /**
     * @throws Exception
     */
    public function create(array $data): ?User
    {
        DB::beginTransaction();
        try {

            $imageName = Str::random(20) .'.'. $data['photo']->extension();
            $destinationPath = 'users' . '/' . $imageName;

            Storage::disk('public')->put(
                $destinationPath,
                file_get_contents($data['photo']->getRealPath())
            );

            $data['photo'] = $imageName;
            $data['password'] = Hash::make('password');
            $user = User::create($data);

            DB::commit();

            return $user;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error($exception->getMessage());
            Log::error($exception->getTraceAsString());
            throw new Exception($exception->getMessage(), 500, $exception);
        }
    }

}
