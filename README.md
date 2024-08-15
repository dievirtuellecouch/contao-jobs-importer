# Contao Jobs Importer to Plenta Jobs Basic

This Contao bundle adds importing of job offers from external sources. Currently only *TalentStorm* is supported. Offers, locations and organizations are imported hourly using Contao’s cron job.

## Configuration

Add the configuration of this bundle to Contao’s `config/config.yaml` file.

```yaml
jobs_importer_to_plenta_basic:
    # Override datePosted attribute of job offer after given threshold (in days).
    # Leave empty to use the value given by the external source.
    override_date_posted_threshold: 3
    sources:
        - type: 'talentstorm'
          api_key: # place the API key here
    # Optional remapping of locations to a specific organization
    mapping:
        organization:
            - label: # label of the location as provided by the API
              id: # ID of the organization
```

## Usage of Custom Employment Types

### TalentStorm

Import TalentStorm’s jobtype field by creating a custom employment type in Plenta, where the title of the employment type equals the label of the job type. The import will then map the job type as employment type instead of the default one’s.
