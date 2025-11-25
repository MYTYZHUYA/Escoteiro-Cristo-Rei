### POST ``/accounts/alive``
**Required Body Parameters:** 
- refresh_token: ``string``

### POST ``/accounts/chief/get-data`` **Authenticated**

### POST ``/accounts/chief/update`` **Authenticated**
**Optional Body Parameters:** 
- password: ``string``
- username: ``string``
- name: ``string``

### POST ``/accounts/get-data`` **Authenticated**

### POST ``/accounts/login``
**Required Body Parameters:** 
- reg: ``string``
- password: ``string``

### POST ``/accounts/quit``
**Required Body Parameters:** 
- refresh_token: ``string``

### POST ``/accounts/refresh``
**Required Body Parameters:** 
- refresh_token: ``string``

### POST ``/accounts/update`` **Authenticated**
**Optional Body Parameters:** 
- password: ``string``
- username: ``string``
- name: ``string``

### POST ``/chief/create-account``
**Required Body Parameters:** 
- reg: ``string``
- password: ``string``
- username: ``string``
- name: ``string``

### POST ``/create-account``
**Required Body Parameters:** 
- reg: ``string``
- password: ``string``
- username: ``string``
- name: ``string``

### GET ``/get-chief/{user_id}``

### GET ``/get-routes``
**Required Body Parameters:** 
- something_required: ``string``
**Optional Body Parameters:** 
- something_optional: ``string``

### GET ``/get-user/{user_id}``

### POST ``/groups/create`` **Authenticated**
**Required Body Parameters:** 
- state: ``string``
- name: ``string``
- num: ``int``

### POST ``/groups/delete`` **Authenticated**

### GET ``/groups/get-user-data/{user_id}``

### POST ``/groups/join/{id_group}`` **Authenticated**

### POST ``/groups/quit`` **Authenticated**

### POST ``/groups/update`` **Authenticated**
**Optional Body Parameters:** 
- state: ``string``
- name: ``string``
- num: ``int``

### POST ``/groups/update-permissions`` **Authenticated**
**Required Body Parameters:** 
- target_user_id: ``int``
- permission_level: ``int``

### GET ``/groups/{id_group}``

### GET ``/groups/{id_group}/members``

