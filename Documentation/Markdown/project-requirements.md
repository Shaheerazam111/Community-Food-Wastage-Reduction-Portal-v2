**Community Food Wastage Reduction Portal**

**Project Domain / Category**

Web Application

**Abstract / Introduction**

In urban environments, a significant amount of surplus food from restaurants, event planners, and households is discarded while local shelters and NGOs struggle to meet the needs of the underprivileged. This project aims to develop a centralized digital bridge that connects food donors with recipients in real-time. By automating the listing and claiming process, the portal eliminates the logistical delays that often lead to food spoilage. The system facilitates the efficient redistribution of surplus resources, turning potential waste into a valuable social asset through community collaboration.

**Functional Requirements**

The application should be able to:

- User Management: Provide secure registration and login for three distinct roles: Donors (Restaurants, Hotels, Individuals), Recipients (NGOs, Shelters, Volunteers), and System Admin.
- Donation Listing: Enable donors to upload surplus food details, including item names, food category (Veg/Non-Veg), quantity (kg or servings), and "Best Before" timestamps.
- Interactive Map/Search: Allow recipients to search for available food donations based on their current geographic location or city.
- Claiming System: Implement a "Claim" feature where an recipient can reserve a listed donation, preventing multiple organizations from attempting to pick up the same item.
- Status Tracking: Provide a real-time workflow to track the donation status from "Available" to "Claimed" and finally "Delivered".
- Notification Engine: Send automated alerts (email or dashboard notifications) to nearby NGOs whenever a new donation is posted in their area.
- Feedback & Rating: Allow recipients to provide feedback on the quality and hygiene of the food received to maintain system trust.

**Inputs**

- Donor Side: Food description, photos (via Firebase or local storage), pickup address (GPS-integrated), quantity, and expiry time.
- Recipient Side: Verification documents (for NGOs), pickup requests, and "delivered" status confirmations.
- Admin Side: Verification status for new donors/NGOs and management of food categories.

**Expected Outputs**

- Donor Dashboard: A summary of total food donated, impact metrics (e.g., "150 meals saved"), and current active listings.
- Recipient Dashboard: A prioritized list of available food nearby and a history of successful pickups.
- Admin Reports: Graphical analytics showing city-wise donation trends, total waste reduced, and number of people fed.
- Real-time Alerts: Instant dashboard notifications for matching requests or successful deliveries.

**Tools**

- Frontend: HTML5, CSS3 (Tailwind CSS or Bootstrap), React.js or Vue.js.
- Backend: Node.js (Express), Python (Flask/Django), or PHP (Laravel).
- Database: MySQL, PostgreSQL, or MongoDB (for flexible listing attributes).
- Additional APIs: Google Maps API (for location mapping) and Firebase (for real-time image storage and database sync).

**Supervisor**

Name: Hasnain Ahmed

Email ID: <hasnain@vu.edu.pk>

Microsoft Teams ID: <tohasnain.bukhari@outlook.com>