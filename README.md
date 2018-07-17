# Scopus Collaboration Checker

## The Scopus REST Abstract Response structure

Example:
```json
{
  "abstract-retrieval-response": {
    "item": {
      "bibrecord": {
        "head": {
          "author-group": [
            {
              "collaboration": {
                "@seq": "1",
                "ce:text": "The Pierre Auger Collaboration",
                "ce:indexed-name": "The Pierre Auger Collaboration"
              }      
            }
          ]
        }
      }
    }
  }
}
```

## CHANGELOG

### 0.0.0.0 - 

- initial commit

### 0.0.0.1 - 17.07.2018

- Added the class HeuristicCollaborationGuesser, which will try to make a guess on the collaboration 
based on the title of the paper and the tags.

Todo

- The test case of for the HeuristicCollaborationGuesser can easily be rewritten using text fixtures, 
but I dont know how they work in PHP yet.

### 0.0.0.2 - 17.07.2018

- Fixed bug in HeuristicCollaborationGuesser: The guess wasnt being cleared with each new state, which 
means if there was a guess previously it would also be used for all the following publications, even though 
their computation didnt bring anything up.
