Feature: Initialize Pocket API session
    In order to work with Pocket APIs
    As a User
    I need to obtain a valid access token

    Scenario: Getting a token
        Given there are no stored credentials
        When I launch the command 'initialize-session' with input:
            | INPUT          |
            | abc-123        |
            | peekpocket:foo |
        Then I got instructions to obtain a Consumer Key
        Then I got instructions to obtain a Token
        Then credentials are stored

        
