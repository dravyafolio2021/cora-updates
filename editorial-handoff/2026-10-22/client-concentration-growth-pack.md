# Cora Daily Organic Growth Handoff — 22 October 2026

Status: **Editorial handoff; not published**  
Primary ICP: agency owners, agency founders, studio principals, and operations/finance leads  
Pillar: **agency client concentration risk and revenue resilience**

## Placement manifest

The coding agent should split this file into the following production paths while preserving the copy and metadata:

- `content/tools/agency-client-concentration-calculator/requirements.md`
- `content/articles/agency-client-concentration-risk.md`
- `content/articles/calculate-client-concentration-agency.md`
- `content/articles/largest-client-percentage-agency.md`
- `content/articles/what-to-do-if-biggest-client-leaves.md`
- `content/articles/diversify-agency-revenue.md`
- `content/articles/client-concentration-agency-valuation.md`
- `content/articles/client-concentration-vs-specialization.md`
- `content/articles/agency-revenue-at-risk.md`
- `content/articles/client-concentration-contract-terms.md`
- `content/articles/90-day-agency-diversification-plan.md`
- `content/guides/agency-client-concentration-risk-playbook.md`
- `content/guides/agency-revenue-resilience-playbook.md`
- `content/guides/agency-big-client-loss-response-plan.md`
- `content/research/2026-10-22-client-concentration-source-ledger.md`

## Evidence and opportunity summary

The latest available connected data covers 24 July–21 October 2026. Search Console returned 190 query/page rows. Existing agency visibility is concentrated around client onboarding and scope creep: “agency client relationship” generated 39 impressions at average position 3.26, while “agency client onboarding” generated three impressions at position 22. No returned query or landing page addressed client concentration, revenue concentration, largest-client exposure, or client-loss planning. GA4 returned 56 landing pages. The agency partner page produced 14 sessions at 92.86% engagement, while the creative-agency use-case produced ten sessions at 30% engagement. Content traffic remains small and GA4 recorded zero key events in the window, so every asset below requires explicit lead-capture and CTA events rather than traffic-only measurement.

Community evidence is unusually direct. Agency owners describe single accounts representing 40%–73% of revenue and the difficulty of maintaining new-business activity while servicing the account. Current agency-finance guidance recommends measuring largest-client and top-five shares, modelling a loss scenario, protecting the important account, and diluting concentration by growing the rest of the book rather than abruptly firing the client. Thresholds vary and are advisory, not regulation; the product must present them as configurable planning bands.

---

# Free tool requirements: Agency Client Concentration & Revenue-at-Risk Calculator

## Product thesis

Agency founders often know who their biggest client is but cannot see the consequences of that dependence until a renewal slips or procurement intervenes. A free calculator should turn a client-revenue list into an immediate concentration diagnosis, a loss scenario, and a practical diversification target. It should not pretend to forecast churn or prescribe a universal “safe” percentage.

## ICP and jobs to be done

Primary users are founders of 2–50 person creative, marketing, software, production, design, and consulting agencies with recurring retainers or repeat project revenue. Secondary users are agency COOs, finance leads, fractional CFOs, and prospective acquirers.

When I enter my client revenue, I want to know how much of the agency depends on one or a few accounts, what happens if the largest account disappears, and how much new revenue I need to dilute the exposure to a target level—without uploading financial statements or creating an account.

## Scope

Version 1 is a browser-only calculator. It accepts up to 100 client rows, calculates exposure locally, supports monthly or annual revenue, and exports a printable one-page risk brief. No accounting integration, prediction, valuation multiple, or legal advice belongs in v1.

### Inputs

Required:

1. Period: monthly, quarterly, or trailing twelve months.
2. Currency: INR, USD, GBP, EUR, AUD, CAD, or custom symbol.
3. Client name or anonymous label.
4. Revenue for the selected period.

Optional:

- Gross profit by client.
- Contract end/renewal date.
- Notice period in days.
- Revenue type: retainer, project, usage, or other.
- Operating costs for the period.
- Cash reserve balance.
- Target maximum largest-client share, default 20% and editable.

### Calculations

- Total revenue = sum of client revenue.
- Client share = client revenue / total revenue.
- Largest-client share, top-three share, and top-five share.
- Herfindahl-Hirschman-style concentration index = sum of squared whole-number percentage shares. Label this “concentration index,” explain the formula, and avoid regulatory interpretations.
- Effective client count = 1 / sum of squared decimal shares.
- Revenue at risk = revenue of selected client or group.
- Post-loss revenue = total revenue − selected revenue.
- Post-loss operating result = post-loss revenue − operating costs, when costs are supplied.
- Runway after loss = cash reserve / monthly post-loss burn, when the result is negative and both inputs exist.
- New revenue required to reach target share while retaining the largest client: `(largest client revenue / target share) − current total revenue`, floored at zero.
- Gross-profit concentration using the same share method when gross profit is supplied.

### Risk bands

Use neutral, configurable defaults:

- Under 15% largest-client share: lower concentration.
- 15%–20%: monitor.
- 20%–30%: material planning risk.
- Above 30%: high dependency.

Display: “Planning bands, not universal rules. Your contracts, margins, cash reserves, pipeline, and agency size change the risk.” Never label an agency safe, insolvent, investable, or uninvestable.

## Workflow and UX

1. Hero states the outcome: “See how much revenue depends on your biggest clients—and what it takes to reduce the risk.”
2. A three-row starter table appears immediately with sample data and an obvious Clear sample action.
3. Users can add rows, paste two columns from a spreadsheet, or upload CSV. Parsing stays local.
4. Results update instantly and show three headline cards: largest client, top-five share, revenue at risk.
5. A horizontal concentration bar visualizes all clients, with accessible text equivalents.
6. Scenario controls let the user remove any client, set a target share, and optionally add costs/reserves.
7. The action plan converts results into three steps: protect, prepare, diversify.
8. Export produces a branded PDF/print view. Email is optional and never required to see results.

## Outputs

- Overall concentration summary.
- Ranked client table with share and revenue.
- Selected client-loss scenario.
- Diversification revenue target.
- Contract/renewal calendar warnings when dates are supplied.
- Five-question founder checklist.
- Shareable URL only if inputs can be encoded without client names; default to no persistence.

## Edge cases and privacy

- Empty, negative, non-numeric, NaN, infinite, and zero-total inputs must return inline errors without a result.
- Duplicate names are allowed but flagged; users can merge them.
- Group related entities manually to avoid treating subsidiaries as independent exposure.
- Currency affects formatting, never risk math.
- More than 100 rows prompts users to aggregate long-tail clients.
- CSV must reject executables and cap file size at 1 MB.
- Never transmit client names or revenue to analytics. Analytics may record row-count buckets and result bands only.
- Do not retain inputs after refresh unless the user explicitly selects local-device save.
- Dates near or past renewal should be warnings, not churn predictions.
- If operating costs exceed current revenue, show the current deficit before the loss scenario.

## SEO landing page

URL: `/tools/agency-client-concentration-calculator/`  
Title: `Agency Client Concentration Calculator — Free Revenue Risk Tool`  
Meta description: `Calculate your largest-client and top-five revenue share, model losing a key account, and see how much new revenue your agency needs to diversify.`  
H1: `Free Agency Client Concentration Calculator`  
Primary keyword: `agency client concentration calculator`  
Secondary: `client concentration risk`, `revenue concentration calculator`, `largest client percentage`, `agency revenue risk`.

The page should include a 60-word definition, formula block, worked example, editable planning bands, FAQ, privacy note, and links to the three guides in this handoff. Add SoftwareApplication schema only if the live tool is functional. Add FAQPage schema only for visible questions and answers. Canonicalize the trailing-slash URL.

## Beehiiv and lead capture

Results must remain ungated. After the result, offer “Get the 90-Day Agency Diversification Workbook” with email capture. Send the workbook and a five-email sequence: diagnosis, contract protection, pipeline target, mid-tier expansion, and monthly review. Pass source=`client_concentration_calculator`, risk_band, target_gap_band, and currency—not revenue or client identity. The CTA should disclose newsletter subscription and provide an unchecked consent box where required.

## Analytics events

- `concentration_tool_view`
- `concentration_sample_cleared`
- `concentration_row_added` with row-count bucket
- `concentration_csv_imported` with valid/invalid row counts
- `concentration_result_viewed` with largest-share band and top-five band
- `concentration_scenario_run` with scenario type
- `concentration_target_changed` with target band
- `concentration_export_clicked`
- `concentration_lead_form_viewed`
- `concentration_lead_submitted`
- `concentration_cora_cta_clicked`

Never attach client names, exact revenue, email, or uploaded-file contents to analytics.

## Acceptance criteria

1. All formulas match a fixture set covering one, two, five, and 100 clients.
2. Pasted spreadsheet data, manual rows, and CSV produce identical results.
3. No input is transmitted before an explicit email submission.
4. Risk bands are editable and carry the planning-not-advice disclaimer.
5. The selected-client loss scenario recalculates revenue, profit, burn, and runway correctly.
6. The diversification target is zero when the current share is already at or below target.
7. Keyboard-only users can add, edit, remove, model, and export.
8. Screen readers receive text equivalents for every chart.
9. The page passes WCAG 2.2 AA checks, Core Web Vitals targets, and mobile layouts at 320 px.
10. SEO metadata, canonical, sitemap, and structured data validate in production.
11. All analytics events fire once with no financial or client PII.
12. The Beehiiv email arrives with the correct source and downloadable workbook.

---

# Article 1 — Agency Client Concentration Risk: The Founder’s Practical Guide

**Keyword / intent:** agency client concentration risk; informational with tool intent  
**SEO title:** Agency Client Concentration Risk: How to Measure and Reduce It  
**Meta description:** Learn how to measure client concentration, model a major account loss, and reduce agency revenue risk without firing your best client.  
**Slug:** `/articles/agency-operations/agency-client-concentration-risk/`  
**Editorial status:** Publish-ready; finance/legal fact check recommended

The client that changed your agency can also become the client that controls it.

A large account creates welcome stability: predictable work, better planning, and a team that develops deep expertise. The risk appears when that account becomes large enough that one procurement decision, leadership change, acquisition, or delayed renewal can force decisions across the whole agency.

Client concentration is the share of revenue tied to one client or a small group of clients. The basic calculation is simple:

`Client revenue ÷ total agency revenue × 100`

If your agency bills ₹1 crore over twelve months and Client A contributes ₹32 lakh, the largest-client concentration is 32%. Repeat the calculation for every client, then add the top three and top five shares. Where possible, calculate the same view using gross profit. A large low-margin client can dominate capacity without contributing the same share of profit.

There is no universal legal limit. Advisory bands such as 15%, 20%, or 30% are planning prompts, not laws. The correct response depends on agency size, contract length, notice period, cash reserves, margins, delivery concentration, and the strength of the pipeline. A four-client specialist agency will naturally look more concentrated than a 100-client firm.

The useful question is not “Is 22% bad?” It is “What decisions would we make if this revenue stopped on the earliest contractually possible date?”

Run that scenario while the relationship is healthy. Remove the client’s revenue, retain only costs that can genuinely disappear, and build a 13-week cash forecast. Note the lowest cash balance, monthly burn, runway, roles tied to the account, unpaid invoices, and work in progress. Then review the contract: renewal date, termination rights, notice, payment timing, IP handover, and transition obligations.

Do not punish a good client for becoming successful. Abruptly refusing profitable work can damage the relationship without creating another source of revenue. The more durable response is growth-first dilution: protect the account, improve its economics, and grow the rest of the book faster.

Start with four moves:

1. Set a target. If the largest client is 32%, choose a realistic next milestone—perhaps 27%—and calculate the additional revenue required while keeping the account.
2. Expand the middle. The fastest diversification often comes from two or three existing clients with proven trust, not a cold pipeline.
3. Keep prospecting when delivery is busy. Concentration usually worsens when the founder stops business development after landing a large retainer.
4. Improve contract resilience. Longer notice, advance billing, clear renewal checkpoints, and documented relationships beyond one sponsor reduce the speed and surprise of a loss.

Track concentration monthly alongside pipeline coverage, cash reserve, margin, and renewal dates. The goal is not a perfectly even client list. It is enough time and optionality to make considered decisions.

**CTA:** Calculate your largest-client and top-five exposure with Cora’s free Agency Client Concentration Calculator, then save the result as a monthly founder metric.

**Internal links:** calculator; agency cash-flow guide; retainer profitability; client renewal readiness; scope-creep guide.  
**Cover/OG brief:** Dark indigo dashboard with one oversized revenue block shrinking as five smaller client blocks grow; label “32% → 20% without firing the client.” Avoid alarm imagery.

**Sources:** [Verbial agency concentration guide](https://verbial.io/guides/agency-client-concentration-risk); [r/agency owner discussion](https://www.reddit.com/r/agency/comments/1guroc8); [Glencoyne professional-services risk guide](https://www.glencoyne.com/guides/professional-services-client-risk).

---

# Article 2 — How to Calculate Client Concentration for an Agency

**Keyword / intent:** how to calculate client concentration; formula/tutorial  
**SEO title:** How to Calculate Client Concentration for Your Agency  
**Meta description:** Use largest-client share, top-five share, gross-profit concentration, and effective client count to understand agency revenue dependency.  
**Slug:** `/articles/finance/calculate-client-concentration-agency/`  
**Editorial status:** Publish-ready

A revenue total tells you how large the agency is. A concentration view tells you how fragile that total may be.

Export client revenue for a consistent period—preferably trailing twelve months for a strategic view and the last three months for a current operating view. Group subsidiaries or brands that ultimately depend on the same budget owner. Otherwise one economic exposure may look like several independent clients.

Create four columns: client, revenue, gross profit, and contract renewal date. Then calculate each client’s share of total revenue. Sort from highest to lowest.

Use four measures:

- Largest-client share: the single biggest account divided by total revenue.
- Top-five share: the five biggest accounts divided by total revenue.
- Gross-profit share: a client’s gross profit divided by total gross profit.
- Effective client count: `1 ÷ sum of squared decimal revenue shares`.

The effective client count distinguishes two agencies with the same number of logos. An agency with ten equal clients has an effective count near ten. An agency with ten clients but one dominant account may behave economically like a much smaller portfolio.

Example: Client A contributes 35%, B 20%, C 15%, D 10%, and the remaining clients 20%. The largest-client share is 35%; top three is 70%. The concentration index using whole-number shares is `35² + 20² + 15² + 10² + ...`. Use the index for trend tracking, not as a regulatory verdict.

Now add time. Compare the same metrics for the last two or three years and the current trailing twelve months. A 25% share falling from 42% is different from a 25% share rising from 12%. Trend reveals whether the rest of the book is catching up.

Finally, model the loss. Remove the largest client at the earliest possible termination date. Recalculate revenue, gross profit, utilisation, cash burn, and runway. Do not assume every cost disappears with the work; payroll, rent, software, and leadership time often remain.

Review the table monthly. Add a comment for changes caused by a one-off project, delayed invoice, renewal, or acquisition. A clean trend is more valuable than a false sense of precision.

**CTA:** Use the free calculator to run the formulas and export a one-page concentration brief.  
**Internal links:** calculator; utilisation guide; client profitability; cash-flow forecast.  
**Cover/OG brief:** Clean spreadsheet-to-dashboard transformation with four metric cards.

**Sources:** [Glencoyne concentration methodology](https://www.glencoyne.com/guides/professional-services-client-concentration); [Stratworth concentration measurement](https://www.stratworthadvisory.com/insights/customer-concentration-risk/).

---

# Article 3 — What Percentage of Agency Revenue Should One Client Represent?

**Keyword / intent:** largest client percentage agency; benchmark clarification  
**SEO title:** How Much Agency Revenue Should Come From One Client?  
**Meta description:** Understand common largest-client planning bands and the contract, margin, cash, and pipeline factors that make the same percentage more or less risky.  
**Slug:** `/articles/agency-operations/largest-client-percentage-agency/`  
**Editorial status:** Publish-ready; benchmark disclaimer required

There is no magic percentage that makes an agency safe. Common advisory conversations begin around 15%–20% for one client and become more urgent above 30%, but those figures are rules of thumb—not regulation and not a substitute for context.

A 25% client on a two-year contract, billed quarterly in advance, with a 90-day notice period and several senior relationships is not the same risk as a 25% client on a cancellable monthly retainer sponsored by one person. Revenue share is the starting point.

Score six factors beside the percentage:

1. Contract runway: how long until renewal or termination can take effect?
2. Payment quality: advance or arrears, on time or habitually late?
3. Margin: does the account fund resilience or consume it?
4. Relationship depth: one champion or several executive and operational links?
5. Delivery dependency: how many people, tools, and vendors are dedicated to it?
6. Replacement capacity: what qualified pipeline and cash runway exist today?

Use bands as a review cadence. Under 15%, monitor normally. From 15% to 20%, review quarterly. From 20% to 30%, maintain a written contingency and active dilution target. Above 30%, make concentration a monthly founder metric and model the loss in cash terms.

Small specialist agencies will often exceed these bands. That does not make the model wrong; it means the agency should price the dependency, negotiate stronger terms, preserve cash, and avoid pretending the risk is diversified.

The right target is one the agency can reach without damaging a profitable account. Calculate how much total revenue is required to bring the client to the chosen share while keeping its revenue constant. That creates a sales goal, not a vague anxiety.

**CTA:** Enter your current revenue mix and set your own target in Cora’s free calculator.  
**Internal links:** calculator; pricing retainers; pipeline coverage; renewal readiness.  
**Cover/OG brief:** Gauge with editable bands and a prominent “Context changes the number” note.

**Sources:** [Verbial planning bands](https://verbial.io/guides/agency-client-concentration-risk); [Sidekick agency client-loss protection](https://www.sidekickaccounting.co.uk/insights/agency-client-loss-protection).

---

# Article 4 — What to Do If Your Agency Loses Its Biggest Client

**Keyword / intent:** agency lost biggest client; urgent problem-solving  
**SEO title:** Agency Lost Its Biggest Client? A 13-Week Response Plan  
**Meta description:** Stabilise cash, protect the team and remaining clients, and replace lost revenue with a calm 13-week agency response plan.  
**Slug:** `/articles/agency-operations/agency-lost-biggest-client/`  
**Editorial status:** Publish-ready

The first day after losing a major account is not the moment for optimistic annual forecasts. It is the moment to establish cash, obligations, and time.

Within 24 hours, confirm the termination date and scope, invoice completed work, document work in progress, and read the contract before promising a transition. Preserve the relationship: a clean exit can produce referrals, future work, and a credible case study.

Within 72 hours, rebuild a 13-week cash forecast without the client’s revenue. Keep final payments only in the weeks they are realistically due. Separate costs into four groups: immediately removable, removable after transition, fixed, and strategically protected. Calculate the lowest cash balance and runway.

Tell the leadership team the same facts at the same time. Share what is known, what is not, and when decisions will be made. Rumour consumes more energy than a difficult number.

In week one, protect remaining revenue. Review every renewal, late invoice, delivery risk, and executive relationship. Do not overwhelm clients with panic-driven upsells. Fix service problems first, then discuss logical expansions.

In weeks two to four, reactivate warm opportunities: former clients, referrals, paused proposals, and adjacent divisions within existing accounts. Build an offer around proven work from the lost account without disclosing confidential information. Use a specific problem, scope, timeline, and outcome.

By week four, decide whether the gap is temporary or structural. Temporary gaps may be bridged with reserves and contractor reductions. Structural gaps require role, service, or cost changes. Delay makes every option worse.

Over the next two months, rebuild the book with a concentration ceiling for new planning, not a reason to reject good work. Keep a weekly view of cash, pipeline, delivery capacity, and top-client share.

**CTA:** Download the Big Client Loss Response Plan and run the loss scenario before making staffing decisions.  
**Internal links:** loss-response guide; cash-flow guide; pipeline coverage; offboarding checklist.  
**Cover/OG brief:** Calm 13-week timeline, not a crisis siren; week 1, week 4, week 13 milestones.

**Sources:** [Business of Money client-loss plan](https://thebusinessofmoney.com.au/guides/losing-your-biggest-customer/); [Verbial cash-flow guide](https://verbial.io/guides/agency-cash-flow-management).

---

# Article 5 — How to Diversify Agency Revenue Without Firing Your Best Client

**Keyword / intent:** diversify agency revenue; strategic how-to  
**SEO title:** How to Diversify Agency Revenue Without Firing Your Best Client  
**Meta description:** Reduce client concentration by expanding mid-tier accounts, protecting business development time, and growing the rest of your agency’s revenue.  
**Slug:** `/articles/growth/diversify-agency-revenue/`  
**Editorial status:** Publish-ready

When founders discover that one client represents 30% of revenue, the instinct is often to shrink the client. That solves the percentage by destroying revenue. The better default is dilution: keep serving the client well while the rest of the book grows faster.

Start with the denominator. If the client pays ₹30 lakh and total revenue is ₹1 crore, its share is 30%. To bring it to 20% while retaining the account, total revenue must reach ₹1.5 crore. The diversification gap is ₹50 lakh.

Break the gap into three engines.

First, expand trusted clients. Identify accounts with good margins, clear outcomes, and unsolved adjacent problems. A measured expansion into analytics, optimisation, maintenance, or a new business unit is often faster than a new logo.

Second, productise the proof. Convert the large client’s successful work into a repeatable offer: a named problem, standard discovery, bounded deliverables, timeline, price range, and evidence. Remove confidential details; keep the operating insight.

Third, reserve founder selling time. The common concentration trap is operational success: the founder becomes busy inside the account and stops building pipeline. Put business development on the calendar before delivery claims every available hour.

Do not diversify into random services. A broader list of weak offers increases operational risk. Diversify clients while keeping a coherent capability and buyer. The strongest new accounts often resemble the best parts of the large client, not an entirely new market.

Review progress monthly. If the dominant client grows too, celebrate the revenue while updating the target. The objective is resilience, not punishing success.

**CTA:** Use the calculator to turn a target percentage into an exact new-revenue goal.  
**Internal links:** calculator; productised services; case study evidence; client expansion.  
**Cover/OG brief:** One large block remains constant while surrounding blocks grow.

**Sources:** [Move at Pace growth-first approach](https://moveatpace.com/insights/agency-client-concentration/); [Sakas & Company agency concentration plan](https://sakasandcompany.com/client-concentration/); [r/Entrepreneur owner discussion](https://www.reddit.com/r/Entrepreneur/comments/pwi9d2).

---

# Article 6 — How Client Concentration Affects Agency Valuation

**Keyword / intent:** client concentration agency valuation; commercial investigation  
**SEO title:** How Client Concentration Can Affect Agency Valuation  
**Meta description:** See why buyers examine largest-client, top-five, margin, contract, and relationship concentration—and how to prepare a clearer risk narrative.  
**Slug:** `/articles/finance/client-concentration-agency-valuation/`  
**Editorial status:** Publish-ready; M&A adviser review recommended

Buyers do not purchase last year’s revenue. They purchase confidence that revenue and profit can continue after ownership changes. Client concentration tests that confidence.

During diligence, expect questions about the largest client, top three and top five shares; gross-profit concentration; contract duration; termination rights; renewal history; relationship ownership; unpaid invoices; and how much delivery knowledge sits with specific team members.

A concentrated agency is not automatically unsellable. A rising share, month-to-month contract, single founder relationship, weak margin, and no replacement pipeline create a very different story from a declining share, long notice, multiple stakeholder relationships, documented delivery, and strong cash generation.

Prepare a three-year revenue and gross-profit table by client. Group related entities. Explain one-off projects separately. Show the current trailing twelve months and the direction of travel. Add renewal dates, notice periods, and relationship coverage.

Then document the resilience plan. Buyers need to see that concentration is measured and actively managed. A plan that says “we will win more clients” is weaker than one with target segments, pipeline coverage, expansion opportunities, accountable owners, and monthly milestones.

Avoid inventing a valuation discount or multiple. Deal structure depends on market conditions and facts. Concentration may lead to more diligence, conditions, earnouts, or holdbacks, but only qualified advisers can assess a specific transaction.

**CTA:** Generate a concentration brief before your next board, lender, or buyer conversation.  
**Internal links:** calculator; agency KPI guide; recurring revenue; documentation playbook.  
**Cover/OG brief:** Due-diligence folder with revenue-share chart and contract calendar.

**Sources:** [Glencoyne due-diligence guide](https://www.glencoyne.com/guides/professional-services-client-concentration); [Stratworth customer concentration](https://www.stratworthadvisory.com/insights/customer-concentration-risk/).

---

# Article 7 — Client Concentration vs Agency Specialization

**Keyword / intent:** client concentration vs specialization; strategic comparison  
**SEO title:** Client Concentration vs Specialization: Don’t Confuse the Two  
**Meta description:** A focused agency can serve one niche without depending on one budget. Learn how to preserve specialization while diversifying client risk.  
**Slug:** `/articles/strategy/client-concentration-vs-specialization/`  
**Editorial status:** Publish-ready

Specialization is a choice about whom you serve and what you do. Concentration is a fact about where revenue comes from. They are related, but they are not the same.

A healthcare performance agency can be highly specialized and still serve 25 independent clients. A generalist studio can be dangerously concentrated if one conglomerate supplies half its work.

The distinction matters because founders sometimes resist diversification, believing it requires abandoning their niche. Usually it does not. The safer goal is more independent budgets inside the same coherent market.

Map concentration at three levels: client, corporate group, and sector. Ten brand names owned by one parent are not ten independent exposures. Neither are five venture-backed clients that depend on the same funding cycle. Sector exposure can be intentional, but it deserves its own scenario.

Preserve specialization by standardising the buyer problem and delivery system. Diversify through adjacent company sizes, geographies, channels, or subsegments—without becoming a menu of unrelated services.

The healthiest specialist agencies can say: “We know this problem unusually well, and no single budget controls our survival.”

**CTA:** Calculate both client and grouped-account concentration, then document the assumptions.  
**Internal links:** niche positioning; ideal client profile; calculator; productised services.  
**Cover/OG brief:** Two-axis matrix: focused offer vs diversified budgets.

**Sources:** [Stratworth economic-group guidance](https://www.stratworthadvisory.com/insights/customer-concentration-risk/); [r/agency concentration discussion](https://www.reddit.com/r/agency/comments/1guroc8).

---

# Article 8 — Revenue at Risk: A Better Agency Metric Than Revenue Alone

**Keyword / intent:** agency revenue at risk; metric education  
**SEO title:** Agency Revenue at Risk: Measure What Could Disappear  
**Meta description:** Add client exposure, notice periods, renewal timing, margin, and pipeline to revenue reporting for a clearer view of agency resilience.  
**Slug:** `/articles/finance/agency-revenue-at-risk/`  
**Editorial status:** Publish-ready

Two agencies can report the same revenue and carry very different risk. One has twenty clients, advance billing, strong margins, and staggered renewals. The other depends on two accounts that can cancel within thirty days. Revenue alone hides the difference.

Revenue at risk is not a prediction that a client will leave. It is the amount exposed to a defined scenario within a defined period.

Build a simple register for each client: current recurring revenue, gross profit, renewal date, notice period, payment status, relationship health, and delivery dependency. Define scenarios such as “revenue cancellable within 90 days,” “revenue renewing this quarter,” and “revenue tied to one corporate group.”

Report four numbers monthly:

- largest-client share;
- top-five share;
- revenue contractually at risk within 90 days;
- qualified pipeline relative to that at-risk revenue.

Keep subjective health scores separate from contractual facts. A green relationship can still face procurement changes. A difficult relationship may have a long committed term. Both matter, but they are different evidence.

Pair the register with cash runway. Exposure without time-to-respond causes panic; runway converts the same event into a managed plan.

**CTA:** Run a revenue-loss scenario and add the resulting exposure band to your monthly founder dashboard.  
**Internal links:** calculator; renewal readiness; cash runway; KPI dashboard.  
**Cover/OG brief:** Revenue card split into committed, renewing, and exposed bands.

**Sources:** [Verbial agency concentration guide](https://verbial.io/guides/agency-client-concentration-risk); [Xeinadin agency case study](https://www.xeinadin.com/office/richmond/case-studies/when-one-client-is-half-your-business-a-twickenham-agency-faces-its-concentration-risk/).

---

# Article 9 — Contract Terms That Reduce Big-Client Risk

**Keyword / intent:** agency contract notice period client concentration; commercial/legal education  
**SEO title:** Contract Terms That Reduce Agency Client Concentration Risk  
**Meta description:** Use notice, billing, renewal, scope, transition, and relationship planning to reduce the shock of losing a major agency client.  
**Slug:** `/articles/legal-contracts/client-concentration-contract-terms/`  
**Editorial status:** Publish-ready; legal review required

A contract cannot make a client permanent. It can give an agency more time, cleaner economics, and a more orderly transition.

For a concentrated account, review six areas with qualified counsel.

Notice period: a longer notice can create time to replace work, but enforceability and commercial acceptance vary.

Billing timing: advance billing reduces the gap between delivery and cash. Clear due dates and late-payment remedies matter most when one invoice is material.

Renewal process: define dates, responsible contacts, and what happens if the parties keep working after expiry. Start renewal before procurement controls the timeline.

Scope and change control: concentration becomes worse when the biggest client also absorbs unpaid work. Price and approve changes explicitly.

Termination and transition: document handover, final invoicing, access removal, IP, data return, and support. A calm exit protects cash and reputation.

Relationship continuity: the contract is only one layer. Maintain several informed stakeholders and document outcomes, decisions, and delivery knowledge inside the agency.

Do not copy terms from a template and assume they fit your jurisdiction or engagement. Use this list as a conversation brief for counsel.

**CTA:** Pair the contract review with the Big Client Loss Response Plan and concentration calculator.  
**Internal links:** commercial contract clauses; offboarding; calculator; renewal guide.  
**Cover/OG brief:** Contract page with six highlighted protective clauses; no gavel imagery.

**Sources:** [Verbial contract-term discussion](https://verbial.io/guides/agency-client-concentration-risk); [Business of Money transition plan](https://thebusinessofmoney.com.au/guides/losing-your-biggest-customer/). Legal review remains mandatory.

---

# Article 10 — A 90-Day Agency Revenue Diversification Plan

**Keyword / intent:** 90 day agency diversification plan; action plan  
**SEO title:** A 90-Day Plan to Reduce Agency Client Concentration  
**Meta description:** Turn a concentration diagnosis into a 90-day plan covering account protection, client expansion, pipeline, cash, and monthly targets.  
**Slug:** `/articles/growth/90-day-agency-diversification-plan/`  
**Editorial status:** Publish-ready

Ninety days will not eliminate a severe concentration problem. It is enough time to replace anxiety with an operating system.

Days 1–10: measure. Build trailing-twelve-month and recent-quarter client shares, gross-profit shares, renewals, notice periods, outstanding invoices, cash reserves, and pipeline. Run the largest-client loss scenario. Choose one realistic concentration milestone.

Days 11–30: protect. Fix delivery risks on the dominant account, establish multiple stakeholder relationships, start renewal early, tighten change control, and document team knowledge. Review the exit clauses with counsel. Protecting the account and reducing dependence are complementary.

Days 31–60: expand. Identify three mid-tier clients with strong fit and unsolved adjacent problems. Run structured discovery before proposing more work. At the same time, turn one proven outcome into a bounded offer and a publishable case study.

Days 61–90: build repeatability. Reserve weekly founder sales time, define a target list, reactivate referrals, and track qualified pipeline against the diversification gap. Keep hiring tied to signed work and capacity evidence.

At day 90, review five metrics: largest-client share, top-five share, gross-profit concentration, qualified pipeline, and cash runway. Celebrate movement, not arbitrary perfection. Reset the next target based on evidence.

**CTA:** Get the complete workbook with weekly tasks, owner fields, formulas, and review templates.  
**Internal links:** calculator; pipeline coverage; case study evidence; client expansion.  
**Cover/OG brief:** 90-day roadmap with Measure, Protect, Expand, Repeat.

**Sources:** [Move at Pace diversification strategies](https://moveatpace.com/insights/agency-client-concentration/); [Sidekick agency resilience guidance](https://www.sidekickaccounting.co.uk/insights/agency-client-loss-protection); [Sakas & Company milestone framework](https://sakasandcompany.com/client-concentration/).

---

# Guide 1 — The Agency Client Concentration Risk Playbook

**Keyword:** agency client concentration risk playbook  
**SEO title:** Agency Client Concentration Risk Playbook  
**Meta description:** A complete operating playbook to measure client dependency, model a major loss, protect key accounts, and reduce concentration over time.  
**Slug:** `/guides/agency-client-concentration-risk-playbook/`  
**Editorial status:** Publish-ready; PDF design and finance/legal review pending

## Chapter structure

1. Why good growth creates hidden dependency
2. Build the concentration register
3. Read the numbers in context
4. Model the loss before it happens
5. Protect the account without becoming captive
6. Dilute risk through deliberate growth
7. Establish the monthly operating cadence

## Full guide

### 1. Why good growth creates hidden dependency

Concentration rarely begins with a reckless decision. It begins when a satisfied client buys more, refers another division, or becomes the account around which the agency builds a team. Revenue rises and the founder’s attention moves from selling to delivery. The danger is not the client’s success. It is the disappearance of alternatives.

Treat concentration as an operating condition, not a moral failure. Your job is to measure it, price it, protect the relationship, and build optionality.

### 2. Build the concentration register

Use a consistent period. For strategy, choose trailing twelve months. For current operations, add the latest quarter. Export revenue by client and add gross profit, contract end, notice, payment terms, outstanding invoices, relationship owners, delivery lead, and corporate group.

Group economically related clients. Sort by revenue share and calculate largest, top-three, and top-five shares. Repeat using gross profit. Calculate the effective client count if you want a single trend metric, but retain the underlying table.

Add a confidence note for every number. One-off pass-through costs, delayed invoices, and project timing can distort revenue shares.

### 3. Read the numbers in context

Planning bands help you decide cadence. They do not decide your fate. A high share deserves deeper questions: How quickly can the revenue stop? How much profit disappears? Which costs remain? How many stakeholder relationships exist? Does the agency own the delivery knowledge? What pipeline could realistically replace the work?

Assess account quality as a separate scorecard covering margin, payment, strategic fit, delivery health, renewal, and relationship depth. Never compress facts and opinions into one unexplained red/amber/green label.

### 4. Model the loss before it happens

Choose the earliest plausible termination date and remove the revenue. Invoice and transition assumptions should follow the contract, not hope. Build a 13-week cash forecast and record the lowest balance, burn, and runway. Mark roles and vendors as immediate, delayed, fixed, or protected costs.

Write decision triggers in advance. Example: if runway falls below twelve weeks, freeze discretionary hiring; if below eight, begin the pre-agreed cost plan. The exact thresholds belong to the business, but the decisions should not be invented during panic.

### 5. Protect the account without becoming captive

Create relationship breadth beyond one sponsor. Document outcomes and decisions. Begin renewals early enough to survive procurement. Review notice, billing, scope, and transition terms with counsel. Maintain delivery quality and margin; unpaid expansion makes dependency worse.

Avoid holding back knowledge to increase switching costs. Ethical stickiness comes from outcomes, trust, integration, and clarity—not hostage tactics.

### 6. Dilute risk through deliberate growth

Calculate the denominator target. If a ₹30 lakh account is 30% and your target is 20%, total revenue must reach ₹1.5 crore. The gap is ₹50 lakh.

Allocate the gap across existing-client expansion, new logos, and repeatable offers. Start with trusted mid-tier accounts, then publish proof that attracts similar buyers. Preserve founder selling time every week, especially while delivery is busy.

### 7. Establish the monthly cadence

On the first working day, refresh revenue and gross profit by client. On the fifth, review renewals and invoices. Mid-month, compare qualified pipeline with the diversification gap. At month-end, update the loss scenario and cash runway.

The founder dashboard should contain largest share, top-five share, gross-profit concentration, effective client count, revenue at risk in 90 days, qualified pipeline, and runway. Assign an owner and next action to every exception.

## CTA and lead magnet

Ungated web guide; gated PDF bundle includes the concentration register, scenario sheet, monthly agenda, and contract-review brief. Beehiiv sequence: measure → model → protect → diversify → review. Primary CTA: “Build your live client and revenue dashboard in Cora.”

## PDF and visual briefs

- PDF: 24-page A4 workbook with fillable tables and a calm finance-operations aesthetic.
- Infographic 1: concentration measurement stack.
- Infographic 2: 13-week loss-scenario flow.
- Infographic 3: growth-first dilution equation.
- Checklist: monthly founder review.

---

# Guide 2 — The Agency Revenue Resilience Playbook

**Keyword:** agency revenue resilience  
**SEO title:** Agency Revenue Resilience Playbook: Clients, Cash and Pipeline  
**Meta description:** Connect concentration, cash reserves, renewals, pipeline, margin, and capacity into one practical revenue resilience system.  
**Slug:** `/guides/agency-revenue-resilience-playbook/`  
**Editorial status:** Publish-ready; PDF design pending

## Chapter structure

1. Define resilience as response time
2. Create the revenue map
3. Connect contracts to cash
4. Size the replacement pipeline
5. Protect delivery capacity
6. Build leading indicators
7. Run the monthly resilience meeting

## Full guide

Revenue resilience is the agency’s ability to absorb a client loss, payment delay, or renewal shock without making rushed decisions that damage the team or remaining clients. It is not the absence of risk. It is time, information, and options.

### 1. Define resilience as response time

Measure how many weeks exist between the earliest revenue stop and the point where cash or capacity forces action. Contract notice, advance billing, reserves, cost flexibility, and pipeline all change this window.

### 2. Create the revenue map

Map every client by revenue, gross profit, payment timing, renewal, notice, corporate group, and delivery team. Add revenue type so retainers and projects are not interpreted the same way. Highlight concentration by revenue and by profit.

### 3. Connect contracts to cash

Build a rolling 13-week cash forecast. Place invoices in the weeks cash is expected, not when revenue is recognised. Show baseline, largest-client loss, and late-payment scenarios. Record assumptions and update them when evidence changes.

Cash reserves should be evaluated against operating costs and concentration. A business with a large cancellable account generally needs more response time than a diversified, advance-billed book. Treat generic reserve rules as starting points, not prescriptions.

### 4. Size the replacement pipeline

The diversification gap is revenue, but pipeline must account for win rate and timing. If the agency needs ₹20 lakh in signed work and historically closes 25% of qualified pipeline, the simple coverage starting point is ₹80 lakh. Adjust for sales-cycle length, start dates, delivery capacity, and project value.

Separate pipeline by evidence: identified, contacted, discovery complete, proposal, verbal, signed. Only qualified stages belong in resilience planning.

### 5. Protect delivery capacity

Do not respond to concentration by hiring ahead of unsigned diversification. Map capacity required for current work, probable pipeline, and loss scenarios. Use contractors or staged start dates where appropriate. Document the knowledge that would let teams move between accounts.

### 6. Build leading indicators

Track renewal slippage, sponsor changes, invoice delays, scope disputes, declining engagement, margin compression, and delivery escalations. None predicts churn alone. Together they tell the founder where to ask better questions.

### 7. Run the monthly meeting

The agenda is fixed: changes in top-client shares; renewals in 180/90/30 days; receivables; pipeline coverage; capacity; cash runway; and decisions. Finish with owners and dates, not observations.

## CTA and lead magnet

Lead magnet: resilience dashboard spreadsheet plus a 13-week forecast template. Beehiiv tags: resilience_guide, concentration_band, team_size_band. Product CTA: “Run clients, pipeline, tasks, invoices, and renewals in one Cora workspace.”

## PDF and visual briefs

- PDF: dashboard-led 28-page field guide.
- Infographic: contract → cash → runway → decision window.
- Chart: baseline vs client-loss cash curve.
- Worksheet: qualified pipeline coverage.

---

# Guide 3 — The Big Client Loss Response Plan for Agencies

**Keyword:** agency biggest client loss plan  
**SEO title:** Big Client Loss Response Plan for Agencies  
**Meta description:** A day-by-day plan for cash, contracts, people, delivery, remaining clients, and revenue replacement after a major agency account leaves.  
**Slug:** `/guides/agency-big-client-loss-response-plan/`  
**Editorial status:** Publish-ready; legal/HR review pending

## Chapter structure

1. First 24 hours: facts and obligations
2. First 72 hours: cash and decision window
3. First week: people and remaining clients
4. Weeks 2–4: replacement motion
5. Weeks 5–13: structural reset
6. Post-event: prevent recurrence

## Full guide

### First 24 hours

Confirm the decision in writing. Establish the contractual end date, notice, final deliverables, transition duties, access, data return, IP, and payment. Do not concede disputed obligations in an emotional call. Assign one executive owner and one operational owner.

Invoice completed milestones and document work in progress. Preserve evidence and client data according to the agreement. Keep communication professional; the departing client may return or refer others.

### First 72 hours

Rebuild the 13-week cash forecast without future revenue from the account. Include final invoices only on realistic collection dates. Identify the lowest cash point, burn, and runway.

Classify costs: tied directly to the work, reducible after transition, fixed, and strategically protected. Record when each decision becomes necessary. Check debt, tax, payroll, and vendor commitments with qualified advisers.

### First week

Brief leaders with facts and a decision calendar. Then communicate with the broader team at the appropriate level. Do not promise there will be no changes unless that is supported by the forecast.

Review every remaining client for renewal, unpaid invoices, delivery risk, and sponsor coverage. Protect service quality. Panic-selling to current clients before fixing their experience can accelerate the problem.

### Weeks 2–4

Prioritise warm routes: active proposals, former clients, referrals, partners, and adjacent divisions. Build a bounded offer from the proof created on the lost account. Prepare a clean case study only where permissions allow.

Set daily and weekly leading measures: conversations, qualified discoveries, proposals, signed value, start dates, and cash collected. Vanity outreach volume is not replacement revenue.

### Weeks 5–13

If the gap remains, execute the pre-agreed structural decisions. Align roles, contractors, software, and space with the realistic book. Preserve capabilities central to the future offer; remove costs that depended on the old account and lack another use.

Continue the transition professionally. Record lessons without blaming individuals. Update pricing, contract, renewal, pipeline, and concentration policies.

### Prevent recurrence

Add concentration and revenue-at-risk to the monthly dashboard. Maintain an evergreen loss scenario for the largest account. Keep founder business-development time protected during busy delivery periods. Diversify budgets, not necessarily capabilities.

## CTA and lead magnet

Lead magnet bundle: 24-hour checklist, 13-week cash template, team communication outline, client-protection list, and warm-pipeline tracker. Beehiiv sequence should be supportive and practical, never exploit urgency. Product CTA: “Centralise clients, contracts, invoices, tasks, and pipeline in Cora before the next renewal shock.”

## PDF and visual briefs

- PDF: 20-page emergency field manual with checkboxes and decision gates.
- Timeline: 24 hours, 72 hours, 7 days, 30 days, 90 days.
- Decision tree: temporary gap vs structural gap.
- One-page wall checklist for founders.

---

# Internal-link and funnel map

The tool links to all ten articles and three guides. Each article links back to the calculator and one guide. Guide 1 is the pillar asset; Guide 2 captures finance/operations intent; Guide 3 captures urgent problem intent. The calculator result promotes Guide 1. Article 4 promotes Guide 3. Articles 2, 3, 6, and 8 promote Guide 2. All lead magnets enter Beehiiv with source-specific tags and a product CTA to `/partners/agencies/` or the free Cora workspace.

# Source notes

- Connected Search Console and GA4, 24 Jul–21 Oct 2026: gap and landing-page evidence described above. Connected data was cached because the analytics plan does not permit forced refresh.
- Current agency/client concentration guidance: Sidekick Accounting, Verbial, Move at Pace, Sakas & Company, Glencoyne, and Stratworth Advisory. Their thresholds are treated as advisory bands rather than universal facts.
- Current community signals: r/agency discussion of a 73% account reduced toward 30%, r/Entrepreneur discussions of >20% dependency, and a 2026 r/EntrepreneurRideAlong post describing a 40% client and an empty pipeline.
- Current client-loss planning: Business of Money’s 13-week scenario and recent agency cash-flow guidance.
- Cora pricing checked 22 Oct 2026: the public page lists Professional at ₹24,990 per year. The same page contains inconsistent GST wording (“includes” near plan cards and “excludes” in the FAQ footer), so editorial copy must not state a tax treatment until the page is corrected.

# Final editorial QA checklist

- Add citations beside any benchmark band that remains in final copy.
- Retain the “planning band, not universal rule” disclaimer.
- Legal-review contract and employment passages.
- Finance-review cash, runway, and valuation passages.
- Do not claim the calculator predicts churn or valuation.
- Validate all internal URLs at implementation time.
- Add Article, Breadcrumb, and visible-FAQ structured data only where supported by on-page content.
- Add author, reviewer, published, and updated dates.
- Generate OG images from the briefs; no stock photos of distressed teams.
- Instrument CTA and lead events before publication.
- Confirm Beehiiv consent language and double-opt-in settings.

