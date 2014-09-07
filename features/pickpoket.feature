Feature: A console application that fetches entries from Pocket
    In order to work with my Pocket entries 
    As a user
    I launch a console application

    Scenario: Launching the application
        Given there is a credentials file
        When I launch the application without parameters
        Then I get an help message

