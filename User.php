<?php
/**
 * MODEL — satu-satunya tempat yang berbicara dengan tabel `users`.
 * Semua query memakai PDO prepared statement.
 * Exception PDO sengaja tidak ditangkap di sini; controller yang mencatatnya
 * ke error_log() dan menampilkan pesan umum ke pengguna.
 */
class User
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** Cari user untuk login: cocok dengan email ATAU username. Termasuk hash password. */
    public function cariUntukLogin(string $identitas): ?array
    {
        $identitas = strtolower(trim($identitas));

        $stmt = $this->pdo->prepare(
            'SELECT id, username, email, nama, role, password
               FROM users
              WHERE email = :email OR username = :username
              LIMIT 1'
        );
        $stmt->execute(['email' => $identitas, 'username' => $identitas]);

        return $stmt->fetch() ?: null;
    }

    /** Data user untuk ditampilkan (tanpa hash password). */
    public function cariById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, username, email, nama, no_hp, tanggal_lahir, role, created_at
               FROM users
              WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function ambilHash(int $id): ?string
    {
        $stmt = $this->pdo->prepare('SELECT password FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $hash = $stmt->fetchColumn();

        return $hash === false ? null : (string) $hash;
    }

    public function usernameTerpakai(string $username, int $kecualiId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE username = :username AND id <> :id');
        $stmt->execute(['username' => strtolower($username), 'id' => $kecualiId]);

        return (bool) $stmt->fetchColumn();
    }

    public function emailTerpakai(string $email, int $kecualiId = 0): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM users WHERE email = :email AND id <> :id');
        $stmt->execute(['email' => strtolower($email), 'id' => $kecualiId]);

        return (bool) $stmt->fetchColumn();
    }

    /** Registrasi: role selalu 'user', password disimpan sebagai hash. Mengembalikan id baru. */
    public function buat(string $nama, string $username, string $email, string $noHp, string $passwordPlain): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, nama, no_hp, password, role)
             VALUES (:username, :email, :nama, :no_hp, :password, :role)'
        );
        $stmt->execute([
            'username' => strtolower($username),
            'email'    => strtolower($email),
            'nama'     => $nama,
            'no_hp'    => $noHp !== '' ? $noHp : null,
            'password' => password_hash($passwordPlain, PASSWORD_DEFAULT),
            'role'     => 'user',
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function ubahProfil(int $id, string $nama, string $username, string $email, string $noHp, ?string $tanggalLahir): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE users
                SET nama = :nama, username = :username, email = :email,
                    no_hp = :no_hp, tanggal_lahir = :tanggal_lahir
              WHERE id = :id'
        );
        $stmt->execute([
            'nama'          => $nama,
            'username'      => strtolower($username),
            'email'         => strtolower($email),
            'no_hp'         => $noHp !== '' ? $noHp : null,
            'tanggal_lahir' => $tanggalLahir,
            'id'            => $id,
        ]);
    }

    public function ubahPassword(int $id, string $passwordPlain): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->execute(['password' => password_hash($passwordPlain, PASSWORD_DEFAULT), 'id' => $id]);
    }

    public function hitungAdmin(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public function hapus(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);

        return $stmt->rowCount() === 1;
    }
}
