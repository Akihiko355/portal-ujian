<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    public array $rows = [];
    public array $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $email = strtolower(trim($row['email'] ?? ''));
            if (empty($email)) {
                $this->errors[] = "Baris kosong: email tidak boleh kosong";
                continue;
            }

            $name = trim($row['nama'] ?? '');
            $phone = trim($row['telepon'] ?? '');
            $dept = trim($row['departemen'] ?? '');
            $password = trim($row['password'] ?? '');
            $nomorUjian = trim($row['nomor_ujian'] ?? '');

            if (empty($name)) {
                $this->errors[] = "{$email}: nama wajib diisi";
                continue;
            }

            $this->rows[] = [
                'nama' => $name,
                'email' => $email,
                'telepon' => $phone,
                'departemen' => $dept,
                'password' => $password,
                'nomor_ujian' => $nomorUjian,
            ];
        }
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
