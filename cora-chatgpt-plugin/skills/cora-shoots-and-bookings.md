# Cora Shoots & Bookings Skill

## Purpose
Manages calendar availability, photography shoot dates, studio bay reservations, and production appointments.

## Workflow Rules
1. **Conflict Checking**:
   - Call `cora_list_bookings` with date parameters before confirming a booking to avoid double bookings.
2. **Booking Confirmation (`cora_create_booking`)**:
   - Provide shoot date, start/end time, location/bay, and package name.
