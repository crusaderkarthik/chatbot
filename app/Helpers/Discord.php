<?php
class Discord {
    public static function notify(string $event, array $ticket): void {
        $webhook = Setting::get('discord_webhook');
        if (!$webhook) return;

        $triggers = json_decode(Setting::get('discord_triggers', '[]'), true) ?? [];
        if (!in_array($event, $triggers)) return;

        $msg = match($event) {
            'created'  => "🎫 New ticket **" . format_ticket_id($ticket['id']) . "**: " . $ticket['subject'],
            'assigned' => "👤 Ticket **" . format_ticket_id($ticket['id']) . "** assigned.",
            'replied'  => "💬 New reply on **" . format_ticket_id($ticket['id']) . "**.",
            'status'   => "🔄 Ticket **" . format_ticket_id($ticket['id']) . "** status changed to `" . $ticket['status'] . "`.",
            'closed'   => "✅ Ticket **" . format_ticket_id($ticket['id']) . "** closed.",
            default    => "📌 Ticket update: " . format_ticket_id($ticket['id']),
        };

        $payload = json_encode(['content' => $msg]);
        $ctx = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\nContent-Length: " . strlen($payload),
                'content' => $payload,
                'timeout' => 5,
            ]
        ]);
        @file_get_contents($webhook, false, $ctx);
    }
}
