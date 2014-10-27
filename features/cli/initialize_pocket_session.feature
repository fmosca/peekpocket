Feature: Initialize Pocket API session
    In order to work with Pocket APIs
    As a User
    I need to obtain a valid access token

    Scenario: Initialize the app by creating a new pocket app and obtaining
              a valid access token.
        Given there are no stored credentials
        When I launch the command 'initialize-session' with input:
            | INPUT          |
            | abc-123        |
            | <enter>        |
        Then I got instructions to create a new app
        Then I got asked the consumer key
        Then I got asket to confirm authorization
        Then credentials are stored

        
