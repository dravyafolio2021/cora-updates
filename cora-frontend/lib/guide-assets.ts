export interface GuideAssetPayload {
  filename: string;
  contentType: string;
  content: string;
}

const agencyOnboardingPack = `# Cora Agency Client Onboarding Pack

A practical implementation pack for agencies and service businesses.

Use this pack as a starting point. Adapt the language, responsibilities, timelines, legal terms, and access requirements to your own business and client engagement.

---

# 1. Client Welcome Email

**Subject:** Welcome — here is how we start

Hi [Client Name],

Great to have you with us.

To get the project moving without unnecessary back-and-forth, we have broken onboarding into four simple steps:

1. Confirm the agreed scope and owners
2. Collect the information and access we need
3. Run the kickoff meeting
4. Start the first delivery cycle

Your onboarding checklist: [LINK]
Kickoff meeting: [DATE / TIME]
Primary agency contact: [NAME / EMAIL / PHONE]

If anything in the checklist is unclear, reply here and we will help.

Regards,
[NAME]
[AGENCY]

---

# 2. Client Information Request Sheet

## Business context
- Company / brand name:
- Website:
- Primary market:
- Main products or services:
- Primary customer segments:
- Current business priority:
- What must improve during this engagement?
- What would make this engagement feel successful?

## Commercial context
- Primary offer(s):
- Average order / project value:
- Current acquisition channels:
- Current sales process:
- Important seasonal / campaign dates:

## Brand context
- Brand guidelines link:
- Logo / identity files:
- Approved fonts:
- Approved colour references:
- Existing campaign / creative library:
- Existing research / customer insights:

## Stakeholders
- Final decision maker:
- Day-to-day contact:
- Finance contact:
- Technical contact:
- Other reviewers:

---

# 3. Access Collection Checklist

Only request access that the engagement actually requires. Prefer delegated access and role-based permissions over sharing primary passwords.

## Website / ecommerce
- [ ] CMS / website access
- [ ] Shopify / ecommerce collaborator access
- [ ] Domain / DNS access if required
- [ ] Analytics access
- [ ] Search Console access

## Advertising
- [ ] Meta Business access
- [ ] Google Ads manager access
- [ ] Merchant Center access if required
- [ ] Other paid media accounts

## CRM / communication
- [ ] CRM access
- [ ] Email marketing platform
- [ ] WhatsApp / customer support platform
- [ ] Existing automation tools

## Creative assets
- [ ] Logo source files
- [ ] Product / service imagery
- [ ] Fonts / brand guidelines
- [ ] Existing ad creatives
- [ ] Video library
- [ ] Testimonials / UGC permissions

## Security rule
Do not collect passwords in an ordinary chat thread or spreadsheet when the platform provides delegated or role-based access.

---

# 4. Scope Alignment Sheet

Complete this before execution begins.

## Outcome
What business outcome is this engagement intended to support?

[WRITE HERE]

## Included deliverables
- [DELIVERABLE 1]
- [DELIVERABLE 2]
- [DELIVERABLE 3]

## Client responsibilities
- Provide required access by [DATE]
- Provide feedback within [TIMEFRAME]
- Nominate one final approval owner
- Supply assets / legal approvals / product information where required

## Agency responsibilities
- [RESPONSIBILITY 1]
- [RESPONSIBILITY 2]
- [RESPONSIBILITY 3]

## Explicitly outside the current scope
- [EXCLUSION 1]
- [EXCLUSION 2]
- [EXCLUSION 3]

## Change rule
When a request changes the agreed deliverables, timeline, capacity, or responsibility, pause execution long enough to classify the request and agree one of three outcomes:

1. Replace an existing item
2. Extend the timeline
3. Approve additional work

Do not let the work change while the agreement remains invisible.

---

# 5. Kickoff Meeting Agenda — 30 to 45 Minutes

## 00–05 min — Context
- Why are we doing this now?
- What changed before this engagement started?

## 05–12 min — Desired outcome
- What result matters most?
- What should not be sacrificed to achieve it?

## 12–20 min — Scope and responsibilities
- Confirm deliverables
- Confirm exclusions
- Confirm client responsibilities
- Confirm agency responsibilities

## 20–27 min — Communication and approvals
- Primary communication channel
- Update cadence
- Final approval owner
- Expected feedback window
- Escalation route

## 27–35 min — First delivery cycle
- First milestone
- Owner
- Due date
- Dependencies
- What the client should expect next

## Close
Repeat the next three actions, owners, and dates before ending the call.

---

# 6. Communication & Approval Rules

Use a simple operating agreement so the team does not negotiate communication norms every week.

## Recommended structure

**Primary channel:** [CHANNEL]

**Weekly status update:** [DAY / TIME]

**Routine response target:** [TIMEFRAME]

**Urgent escalation:** [METHOD]

**Final approval owner:** [NAME / ROLE]

**Feedback rule:** Consolidate feedback through the nominated owner wherever possible.

**Decision rule:** Decisions that affect scope, cost, timeline, or final output must be recorded in writing.

**Approval rule:** A deliverable is not considered approved until the authorised reviewer confirms approval in the agreed channel.

---

# 7. Internal Agency Handoff Checklist

Before the delivery team begins, confirm that sales has handed over the engagement properly.

- [ ] Signed / approved proposal is accessible
- [ ] Scope and exclusions are clear
- [ ] Commercial terms are understood
- [ ] Client goals are summarised in plain language
- [ ] Key stakeholders are identified
- [ ] Final approval owner is identified
- [ ] Important promises made during sales are documented
- [ ] Required client access has been requested
- [ ] Known risks / dependencies are documented
- [ ] First milestone has an owner and due date
- [ ] Communication cadence is scheduled
- [ ] Project workspace has been created

**Internal handoff question:**
If the salesperson disappeared for two weeks, would the delivery team still understand what was sold and what happens next?

If the answer is no, the handoff is incomplete.

---

# 8. 30-Day Onboarding Roadmap

## Day 0 — Agreement
- Confirm scope
- Confirm commercials
- Confirm owners
- Send welcome message

## Days 1–2 — Intake
- Collect business context
- Collect assets
- Request delegated platform access
- Resolve missing prerequisites

## Days 2–4 — Kickoff
- Run kickoff
- Confirm communication rules
- Confirm approval owner
- Confirm first delivery milestone

## Week 1 — First visible progress
- Produce / configure the first meaningful piece of work
- Share status before the client needs to ask
- Surface blockers immediately

## Week 2 — First structured review
- Review progress against the agreed outcome
- Collect consolidated feedback
- Log any requested changes

## Week 3 — Stabilise the working rhythm
- Confirm recurring meeting / reporting cadence
- Fix recurring access or approval bottlenecks
- Clarify any responsibility gaps

## Week 4 — Onboarding retrospective
Ask internally:
- What created unnecessary back-and-forth?
- What did the client ask twice?
- Which access should have been collected earlier?
- Which responsibility was unclear?
- Which step can become a reusable automation or template?

Then update the onboarding SOP before the next client arrives.

---

# 9. Change Request Mini-Template

**Requested change:**
[DESCRIBE REQUEST]

**Why it is being requested:**
[CONTEXT]

**Impact on current scope:**
[NO CHANGE / REPLACES ITEM / ADDS WORK]

**Impact on timeline:**
[IMPACT]

**Commercial impact:**
[IMPACT]

**Decision:**
[APPROVED / REJECTED / DEFERRED]

**Approved by:**
[NAME]

**Date:**
[DATE]

---

# 10. Weekly Client Status Template

**This week**
- Completed:
- In progress:
- Waiting on client:

**Decisions needed**
1. [DECISION]
2. [DECISION]

**Risks / blockers**
- [BLOCKER]

**Next week**
- [NEXT ACTION]

**Scope changes raised this week**
- [NONE / CHANGE]

---

# How to use this pack with Cora

The templates work independently of any software. If you use Cora, keep the proposal, client requirements, approvals, project delivery, and commercial context connected so the team does not have to reconstruct the engagement from scattered tools.

Learn more: https://heycora.in/agency-management-software-india/

---

Prepared by Cora / HeyCora
https://heycora.in/
`;

export const GUIDE_ASSET_PAYLOADS: Record<string, GuideAssetPayload> = {
  'agency-onboarding-pack': {
    filename: 'cora-agency-client-onboarding-pack.md',
    contentType: 'text/markdown; charset=utf-8',
    content: agencyOnboardingPack,
  },
};

export function getGuideAssetPayload(assetId: string): GuideAssetPayload | undefined {
  return GUIDE_ASSET_PAYLOADS[assetId];
}
