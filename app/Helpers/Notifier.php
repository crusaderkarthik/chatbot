<?php
class Notifier {
    public static function notifyWatchers(array $ticket, string $event, string $message): void {
        $watchers = Watcher::forTicket($ticket['id']);
        foreach ($watchers as $w) {
            $prefs = json_decode($w['notification_prefs'] ?? '{}', true);
            if ($prefs['email'] ?? true) {
                Mailer::send($w['email'], 'Ticket Update: ' . format_ticket_id($ticket['id']), $message);
            }
        }
        Discord::notify($event, $ticket);
    }

    public static function notifyMentions(string $text, array $ticket): void {
        preg_match_all('/@(\w+)/', $text, $matches);
        foreach ($matches[1] as $username) {
            $user = Database::fetch('SELECT * FROM users WHERE name=?', [$username]);
            if ($user) {
                Watcher::add($ticket['id'], $user['id']);
                Mailer::send($user['email'], 'You were mentioned in ' . format_ticket_id($ticket['id']), "You were mentioned in ticket: " . $ticket['subject']);
            }
        }
    }
}
