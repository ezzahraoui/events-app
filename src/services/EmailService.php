<?php
class EmailService
{
    private static string $fromEmail = 'noreply@events.com';
    private static string $fromName = 'Application Événements';

    public static function sendRegistrationConfirmation(int $userId, int $eventId): bool
    {
        $user = User::findById($userId);
        $event = Event::findById($eventId);

        if (!$user || !$event) {
            return false;
        }

        $to = $user->getEmail();
        $subject = "Confirmation d'inscription - " . $event->getTitle();

        $message = self::buildRegistrationEmail($user, $event);
        $headers = self::buildHeaders();

        return mail($to, $subject, $message, $headers);
    }

    public static function sendEventReminder(int $userId, int $eventId): bool
    {
        $user = User::findById($userId);
        $event = Event::findById($eventId);

        if (!$user || !$event) {
            return false;
        }

        $to = $user->getEmail();
        $subject = "Rappel - " . $event->getTitle();

        $message = self::buildReminderEmail($user, $event);
        $headers = self::buildHeaders();

        return mail($to, $subject, $message, $headers);
    }

    public static function sendEventCancellation(int $userId, int $eventId): bool
    {
        $user = User::findById($userId);
        $event = Event::findById($eventId);

        if (!$user || !$event) {
            return false;
        }

        $to = $user->getEmail();
        $subject = "Annulation d'événement - " . $event->getTitle();

        $message = self::buildCancellationEmail($user, $event);
        $headers = self::buildHeaders();

        return mail($to, $subject, $message, $headers);
    }

    private static function buildRegistrationEmail(User $user, Event $event): string
    {
        $message = "Bonjour " . $user->getFirstName() . ",\n\n";
        $message .= "Votre inscription à l'événement \"" . $event->getTitle() . "\" est confirmée.\n\n";
        $message .= "📅 Date: " . $event->getEventDate()->format('d/m/Y à H:i') . "\n";
        $message .= "📍 Lieu: " . $event->getLocation() . "\n";
        $message .= "👥 Capacité: " . $event->getCapacity() . " places\n\n";

        $message .= "Description de l'événement:\n";
        $message .= $event->getDescription() . "\n\n";

        $message .= "Merci de votre participation !\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe Application Événements";

        return $message;
    }

    private static function buildReminderEmail(User $user, Event $event): string
    {
        $message = "Bonjour " . $user->getFirstName() . ",\n\n";
        $message .= "Ceci est un rappel pour l'événement auquel vous êtes inscrit:\n\n";
        $message .= "📅 Date: " . $event->getEventDate()->format('d/m/Y à H:i') . "\n";
        $message .= "📍 Lieu: " . $event->getLocation() . "\n";
        $message .= "📝 Titre: " . $event->getTitle() . "\n\n";

        $message .= "N'oubliez pas d'être présent à l'heure !\n\n";
        $message .= "À bientôt,\n";
        $message .= "L'équipe Application Événements";

        return $message;
    }

    private static function buildCancellationEmail(User $user, Event $event): string
    {
        $message = "Bonjour " . $user->getFirstName() . ",\n\n";
        $message .= "Nous vous informons que l'événement \"" . $event->getTitle() . "\" a été annulé.\n\n";
        $message .= "📅 Date prévue: " . $event->getEventDate()->format('d/m/Y à H:i') . "\n";
        $message .= "📍 Lieu: " . $event->getLocation() . "\n\n";

        $message .= "Nous sommes désolés pour ce désagrément et vous tiendrons informés si l'événement est reprogrammé.\n\n";
        $message .= "Votre inscription a été automatiquement annulée.\n\n";
        $message .= "Cordialement,\n";
        $message .= "L'équipe Application Événements";

        return $message;
    }

    private static function buildHeaders(): string
    {
        $headers = "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return $headers;
    }

    public static function setFromEmail(string $email): void
    {
        self::$fromEmail = $email;
    }

    public static function setFromName(string $name): void
    {
        self::$fromName = $name;
    }

    public static function testEmail(): bool
    {
        $testEmail = 'test@events.com';
        $subject = 'Test Email Service';
        $message = 'Ceci est un email de test pour vérifier que le service d\'envoi d\'emails fonctionne correctement.';
        $headers = self::buildHeaders();

        return mail($testEmail, $subject, $message, $headers);
    }
}
