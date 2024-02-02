# Contao Jobs Importer to Plenta Jobs Basic

This Contao bundle adds importing of job offers from external sources. Currently only *TalentStorm* is supported. Offers, locations and organizations are imported hourly using Contao’s cron job.

## Configuration

Add the configuration of this bundle to Contao’s `config/config.yaml` file.

```yaml
jobs_importer:
    sources:
        - type: 'talentstorm'
          api_key: # place the API key here
    # Optional remapping of locations to a specific organization
    mapping:
        organization:
            - label: # label of the location as provided by the API
              id: # ID of the organization
```
