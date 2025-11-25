# 📋Índice:
- ``/get-user/{user_id}``
- ``/create-account``
- ``/accounts/get-data``
- ``/accounts/update``
- ``/get-chief/{user_id}``
- ``/chief/create-account``
- ``/accounts/chief/get-data``
- ``/accounts/chief/update``

# Normal User Routes

### GET ``/get-user/{user_id}``

### POST ``/create-account``
**Required Body Parameters:** 
- reg: ``string``
- password: ``string``
- username: ``string``
- name: ``string``

### POST ``/accounts/get-data`` **Authenticated**

### POST ``/accounts/update`` **Authenticated**
**Optional Body Parameters:** 
- password: ``string``
- username: ``string``
- name: ``string``

# Chief Routes

### GET ``/get-chief/{user_id}``

### POST ``/chief/create-account``
**Required Body Parameters:** 
- reg: ``string``
- password: ``string``
- username: ``string``
- name: ``string``

### POST ``/accounts/chief/get-data`` **Authenticated**

### POST ``/accounts/chief/update`` **Authenticated**
**Optional Body Parameters:** 
- password: ``string``
- username: ``string``
- name: ``string``
