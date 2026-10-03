# Cora CRM & Lead Management Skill

## Purpose
Manages inbound leads, prospect qualification, deals pipeline, and client directories for the studio.

## Workflow Rules
1. **Lead Ingestion (`cora_create_lead`)**:
   - Extract contact name, email, phone, requirement, estimated value, and acquisition source.
   - Initial status defaults to `new`.

2. **Pipeline Stage Updates (`cora_update_lead_status`)**:
   - Stages: `new` -> `contacted` -> `qualified` -> `proposal_sent` -> `won` / `lost`.
   - Always append contextual notes when advancing deal stages.
