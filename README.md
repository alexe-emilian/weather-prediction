 ### PROJECT SETUP 
- Requirements for the project: PHP, Git, Composer, Symfony binary (which can be downloaded from here: https://symfony.com/download)
- Clone the repository via Git
- Install all dependencies via composer
- Start the application with: `symfony server:start`

### PROJECT DESIGN
- In order to easily accommodate for new sources, a chain of responsibility pattern is used to handle the request
- `WeatherPrediction:getData()` is the method that iterates through all the current source implementations
- All the Partner input sources implement the interface `PartnerInputInterface`
- Thanks to the configuration in `config\services.yaml` which is detailed below, services that implement the `PartnerInputInterface` are automatically tagged. That, in combination with the configuration for `WeatherPrediction`, any new source that implements the mentioned interface will be added into the flow automatically.
```
_instanceof:
        App\Client\PartnerInputInterface:
            tags: [ 'app.partner_weather_input' ]

App\Service\WeatherPrediction:
        arguments:
            - !tagged_iterator app.partner_weather_input
```
- The partner sources were mocked, as suggested in the assignment, and a cache layer was added for them
- Temperature scale conversion is done in `TemperatureScaleConverter`

### ADDING A NEW SOURCE
- As mentioned in the `PROJECT DESIGN` section, any new source only needs to implement the interface `PartnerInputInterface`, and the data will be added into the existing flow

### Notes
- Class diagram can be found in `resources\class_diagram.puml`
- PlantUML (https://plantuml.com/) is used to draw the diagram, an easy way to view it is through the PhpStorm plugin `PlantUML integration` 
- Example endpoint to test the application: `weather?city=Amsterdam&date=2021-10-06&scale=celsius`
- Note that the only parameter that changes the response values for the predictions is `scale` due to the data being mocked
- The essential parts to cover with unit tests, if the target coverage would be `80%`, are the elements that are part of the critical flow
    - Classes found in `src\Service`, `src\Client` and `src\Factory`, for example
    - This way we can ensure that the important components have the expected functionality