# smarttap

This is an automatic smart tap IOT management system that tracks various parameters ie `ph, turbility, tds, temperature, security` for ease identification weather the water is safe for use by the company clients or not. And more about the system is that it also tracks water usage from clients to the company for easy demand for payments per unit.

## Users

- [x] Represent both administrators on the company.

```
- GET: Returns all admins. 
- POST: Adds new admintrator. 
- DELETE: Deletes admins from the system.
```

## Administrator

- [x] Represents users/workers/management to the company.
- [x] These can stop any post-paid accounts from using the water with a help of setting the due-date.
- [x] These are able to view clients records ie water usage, pay bills if any, detect water quality with help of security measures, water parameters eg ph, turbidity together with the number of clients in the system.

## Clients

- [x] Represents users/customers to the company.
- [x] These are able to view their records ie water usage, pay bills if any, detect water quality and the number of sub-clients their have.

```
- GET: Returns all clients details. 
- POST: Adds new client to the system.
- PUT/PATCH: Updates clients details. 
- DELETE: Deletes clients from the system.
```

## Accounts

- [x] Represents that clients have been assigned meter numbers, and so administrator can keep track of their water usage in relation to their payment.

## Customers

- [x] Represents users that make use of the clients service.

## Authors

- Matembu Emmanuel Dominic
- Ojoko Rogers
- Joshua K
