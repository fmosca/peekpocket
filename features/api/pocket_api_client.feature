Feature: Read Pocket entries 
    In order to work with my saved entries
    As a developer
    I need to read entries from Pocket's API

    Scenario: Read the last saved items
        Given I saved an entry with url "http://example.com"
        And there is a initialized Pocket client
        When I ask for the last 5 items
        Then the first element of the result array must have url "http://example.com" 
