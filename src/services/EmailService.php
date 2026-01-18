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



    private static function buildHeaders(): string
    {
        $headers = "From: " . self::$fromName . " <" . self::$fromEmail . ">\r\n";
        $headers .= "Reply-To: " . self::$fromEmail . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return $headers;
    }


}
