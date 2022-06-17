CONTENTS OF THIS FILE
---------------------
 * Introduction
 * Career profile importer from
 * Breadcrumb
 * Entity Compare Controller extend

INTRODUCTION
------------

This module is to extent the contrib and add custom query/hook

CAREER PROFILE Importer FORM
----------------------------
Custom form to import CSV for career profile content. /admin/config/development/csv-importer
NOCcode is unique, Importer will check if NOC is there, if yes, it will update the node.
Format:
NOCCode,Title,ProfileImageUrl,FirstVideo,SecondVideo
#0011,Legislators,https://careercompass-dev.workbc.ca/noc_image/0011-NOC-profile.png,NULL,NULL


BREADCRUMB
----------
implements BreadcrumbBuilderInterface to set for quiz pages.

Entity Compare Controller extend
--------------------------
Extend Entity compare controller to remove session value and set carrer to session for Each quiz type

.module
---------------

Hook_view_query_alter() to get value from session for quiz result and render career match 