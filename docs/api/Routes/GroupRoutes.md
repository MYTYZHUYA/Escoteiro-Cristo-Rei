# 📋Índice:
- ``/groups/create``
- ``/groups/delete``
- ``/groups/get-user-data/{user_id}``
- ``/groups/join/{id_group}``
- ``/groups/quit``
- ``/groups/update-permissions``
- ``/groups/{id_group}``
- ``/groups/{id_group}/members``

# Group Routes

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
