<?php
require_once APP . '/Core/Model.php';

class Attachment extends Model {
    protected static string $table = 'attachments';

    const ALLOWED_EXTENSIONS = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','ppt','pptx','txt','csv','zip','mp4','mov'];
    const MAX_SIZE_BYTES = 26214400; // 25MB

    public static function upload(array $file, int $ticketId, int $userId): ?int {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;
        if ($file['size'] > self::MAX_SIZE_BYTES) return null;

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXTENSIONS)) return null;

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = ROOT . '/public/uploads/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $dest)) return null;

        return Database::insert(
            'INSERT INTO attachments (ticket_id, user_id, filename, original_name, size, created_at) VALUES (?,?,?,?,?,NOW())',
            [$ticketId, $userId, $filename, $file['name'], $file['size']]
        );
    }

    public static function forTicket(int $ticketId): array {
        return Database::fetchAll('SELECT * FROM attachments WHERE ticket_id=? ORDER BY created_at', [$ticketId]);
    }
}
