**Prototype Assignment**

_Project: Community Food Wastage Reduction Portal_

**1\. Assignment Overview**

This assignment focuses on building the core engine of the Community Food Wastage Reduction Portal. Instead of working on complex admin screens or visual reports, your goal is to construct the main operational workflow: registering users, posting surplus food, searching nearby donations, and claiming food safely.

**2\. TO-DO List (What You Need to Build)**

**TASK 1: User Signup & Authentication**

- Build secure user registration and login endpoints (using JWT or sessions).
- Create two distinct user profiles: Donor (Business Name, Address, Phone, Latitude, Longitude) and Recipient (NGO Name, Contact Person, Address, Latitude, Longitude).

**TASK 2: Food Listing Lifecycle (Donor Side)**

- Create a food entry form where Donors input: Food Title, Category (Veg/Non-Veg), Quantity, Pickup Address, Latitude, Longitude, and Expiry Date/Time.
- Ensure listing status transitions strictly in order: AVAILABLE ──> CLAIMED ──> DELIVERED.

**TASK 3: Nearby Food Search (Recipient Side)**

- Build a search feature where Recipients can see available food items near their current location.
- Add distance radius filtering (e.g., show food within 5 km or 10 km).
- Filter out any food that is already claimed or past its expiry date.

**TASK 4: Safe Food Claiming Workflow**

- Allow a Recipient to claim an available food listing.
- Ensure claiming is safe against simultaneous clicks so that two users can NEVER claim the exact same item.

**3\. Understanding the Difficult Parts (Key Challenges)**

You are expected to figure out and implement solutions for these three technical challenges:

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Challenge 1: Calculating Distance for Nearby Search</strong></p><ul><li>What makes it tricky: You have a recipient's location and many food listings stored in your database. You must return only food listings within X kilometers without slowing down your app.</li><li>What you need to solve: If using SQL (MySQL/PostgreSQL), learn how to calculate distances in your SQL query using trigonometric math (Haversine formula). If using MongoDB, set up a 2dsphere location index and query using native geospatial operators ($near).</li><li>Your Goal: Deliver search results sorted from closest to furthest.</li></ul></th></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Challenge 2: Preventing Double-Claiming (Race Conditions)</strong></p><ul><li>What makes it tricky: If Recipient A and Recipient B press 'Claim' on the exact same food item at the exact same millisecond, simple 'check-then-update' code will let BOTH claims succeed.</li><li>What you need to solve: You must make the claim action atomic in your database. Perform an update query that checks that status is still 'AVAILABLE' in the update itself. If zero records were modified, return an error to the second user stating the item was already taken.</li><li>Your Goal: Prevent double-booking completely under heavy simultaneous traffic.</li></ul></th></tr></tbody></table></div>

<div class="joplin-table-wrapper"><table><tbody><tr><th><p><strong>Challenge 3: Handling Expired Food Cleanly</strong></p><ul><li>What makes it tricky: Food that expires at 5:00 PM must not be visible or claimable at 5:01 PM.</li><li>What you need to solve: Apply 'lazy filtering' by always comparing the expiry timestamp against the current server time in your database search query. Optionally, run a background cron job once an hour to mark old items as EXPIRED in the database.</li><li>Your Goal: Ensure recipients never see or receive spoiled food.</li></ul></th></tr></tbody></table></div>

**4\. Submission & Grading Criteria**

You need to submit the complete source code.