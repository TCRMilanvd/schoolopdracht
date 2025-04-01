<?php

// Auteur: Emily van den Berg
// Functie: Unit testen voor de User class

use PHPUnit\Framework\TestCase;
use classes\User;

class UserTest extends TestCase
{
    // Test 1: Kijken of de gebruiker correct wordt aangemaakt
    public function testValidateUserValid()
    {
        $user = new User();
        $user->username = 'geldigeUser';  // Geldige gebruikersnaam
        $user->SetPassword('geldigWachtwoord123');  // Geldig wachtwoord via setter ivm private gebruik
        $user->email = 'geldig@geldig.nl';  // Geldig e-mailadres

        $errors = $user->ValidateUser();

        $this->assertEmpty($errors, 'Test error gevonden: Gebruiker hoort geldig te zijn.');
    }

    // Test 2: Kijken of er een error komt als de username leeg is
    public function testValidateUserUsernameEmpty()
    {
        $user = new User();
        $user->username = '';  // Lege gebruikersnaam
        $user->SetPassword('geldigWachtwoord123');  // Geldig wachtwoord via setter ivm private gebruik
        $user->email = 'geldig@geldig.nl';  // Geldig e-mailadres

        $errors = $user->ValidateUser();

        $this->assertContains('Gebruikersnaam mag niet leeg zijn.', $errors, 'Test error gevonden: Gebruikersnaam hoort leeg te zijn.');
    }

    // Test 3: Kijken of er een error komt als de username te kort is
    public function testValidateUserUsernameTooShort()
    {
        $user = new User();
        $user->username = 'a';  // Gebruikers naam is te kort (minder dan 3 tekens)
        $user->SetPassword('geldigWachtwoord123');  // Geldig wachtwoord via setter ivm private gebruik
        $user->email = 'geldig@geldig.nl';  // Geldig e-mailadres

        $errors = $user->ValidateUser();

        $this->assertContains('Gebruikersnaam moet tussen 3 en 50 tekens lang zijn.', $errors, 'Test error gevonden: Gebruikersnaam hoort te kort te zijn.');
    }

    // Test 4: Kijken of er een error komt als de username te lang is
    public function testValidateUserUsernameTooLong()
    {
        $user = new User();
        $user->username = str_repeat('a', 51);  // Gebruikers naam is te lang (meer dan 50 tekens)
        $user->SetPassword('geldigWachtwoord123');  // Geldig wachtwoord via setter ivm private gebruik
        $user->email = 'geldig@geldig.nl';  // Geldig e-mailadres

        $errors = $user->ValidateUser();

        $this->assertContains('Gebruikersnaam moet tussen 3 en 50 tekens lang zijn.', $errors, 'Test error gevonden: Gebruikersnaam hoort te lang te zijn.');
    }

    // Test 5: Kijken of er een error komt als het wachtwoord leeg is
    public function testValidateUserPasswordEmpty()
    {
        $user = new User();
        $user->username = 'geldigeUser';  // Geldige gebruikersnaam
        $user->SetPassword('');  // Geldig wachtwoord via setter ivm private gebruik
        $user->email = 'geldig@geldig.nl';  // Geldig e-mailadres

        $errors = $user->ValidateUser();

        $this->assertContains('Wachtwoord mag niet leeg zijn.', $errors, 'Test error gevonden: Wachtwoord hoort leeg te zijn.');
    }

    // Test 6: Kijken of er een error komt als het e-mailadres ongeldig is
    public function testValidateUserInvalidEmail()
    {
        $user = new User();
        $user->username = 'geldigeUser';  // Geldige gebruikersnaam
        $user->SetPassword('geldigWachtwoord123');  // Geldig wachtwoord via setter ivm private gebruik
        $user->email = 'ongeldig-email';  // Ongeldig e-mailadres

        $errors = $user->ValidateUser();

        $this->assertContains('Ongeldig e-mailadres.', $errors, 'Test error gevonden: E-mailadres hoort ongeldig te zijn.');
    }

    // Test 7: Kijken of er een error komt als alle velden leeg zijn
    public function testValidateUserEmpty()
    {
        $user = new User();
        $user->username = '';  // Lege gebruikersnaam
        $user->SetPassword('');  // Leeg wachtwoord
        $user->email = '';  // Leeg e-mailadres

        $errors = $user->ValidateUser();

        $this->assertContains('Gebruikersnaam mag niet leeg zijn.', $errors, 'Test error gevonden: Gebruikersnaam hoort leeg te zijn.');
        $this->assertContains('Wachtwoord mag niet leeg zijn.', $errors, 'Test error gevonden: Wachtwoord hoort leeg te zijn.');
        $this->assertContains('Ongeldig e-mailadres.', $errors, 'Test error gevonden: E-mailadres hoort leeg te zijn.');
    }
}
