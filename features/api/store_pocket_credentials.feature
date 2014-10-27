Feature: Store Pocket credentials
    In order to work with Pocket APIs
    As a developer
    I need to store and retrieve credentials

    Scenario: Reading credentials
        Given there is a credentials file
        And the value 'consumer_key' is set to 'foo'
        When I load the credentials file
        Then the consumer key should be 'foo'

    Scenario: Writing credentials
        Given there is an empty credentials file
        When I set the consumer key to 'foo'
        And I save the credentials file
        Then the value 'consumer_key' must be stored as 'foo'

