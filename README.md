# AltSwitcher
![](https://github.com/matiasdamiandelsolar/AltSwitcher/blob/main/icon.png)

AltSwitcher is a powerful plugin for PocketMine-MP that allows you to switch between multiple accounts without having to log out and back in.

## Key Features

### Account Grouping
Group multiple accounts together, so players can switch between them easily without logging out.

### Unified Account Banning
The plugin detects when one account is banned and automatically prevents all alts from entering the server.
### Extensive Configuration
Offers a wide range of configurable options such as transfer server and alt limits.

## Commands
- `/account`: Displays a form where you can manage your accounts or group a new account.
- `/account manage`: Allows you to manage grouped accounts, including switching or ungrouping accounts.
- `/account group <account>`: Group the specified account with your current account.
- `/account ungroup <account>`: Ungroup the specified account from your group.

## How to use
1. Group an account: To group an account, simply use /account group <account>. The other account must also type `/account group <your_account>` to complete the grouping process.
2. Manage accounts: Use `/account manage` to manage your grouped accounts. This will allow you to either switch between accounts or ungroup an account.
3. Switch accounts: If you have multiple accounts grouped, simply use `/account` to bring up the account form. From there, you can choose which account to switch to.


## Configuration file
```yml
# Configuration file for AltSwitcher
account-switch:
  
  # Should players be transferred to a server when they switch accounts? (true/false)
  transfer-on-switch: true
  # The IP address of the server players will be transferred to upon account switch.
  server-ip: "127.0.0.1"

  # The port number of the server players will be transferred to.
  server-port: 19132

  # Should all accounts in a group be banned if any account in the group is banned? (true/false)
  # If set to true, banning any account in the group will result in all accounts being banned.
  ban-alts: true

groups:

  # Allow players to ungroup their accounts? (true/false)
  allow-ungroup: true

  # The maximum number of accounts allowed in a single group
  # 2 means 1 alt account
  # 3 means 2 alt accounts and so on.
  max-group-size: 2
```

## Dependencies
- [LangManager](https://github.com/matiasdamiandelsolar/LangManager)
- [FormAPI](https://github.com/jojoe77777/FormAPI)

## Licensing information
This project is licensed under LGPL-3.0. Please see the LICENSE file for details.