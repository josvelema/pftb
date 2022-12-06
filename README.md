
> Documentation created December 2022 by [Jos Velema](https://github.com/josvelema/) - <rjvelemail@gmail.com>

---
# 1. Strategy

### 1. Website/shop for book and workshops 'Painting for the brain'
#### Launch MVP site first and inform potential customers via marketing strategy

> Site as 'MVP' to be launched 9-12-2022, with information and announcements about the book and workshop.
> For now the website will be in English and Dutch. When the book translation in Portuguese is done, the website should also be available in Portuguese.
> The book will be available before 2nd quarter 2023 in digital and physical format , website will provide 'teasers' and previews.
> Some more eye-candy or exercises from the book would work great for getting more attention.


### 2. Newsletter
#### update subscribers about upcoming releases of the book and information about upcoming workshops (dates, availablility, etc.).
> A CMS is already available for sending out mails to subscribers as well as the feature to start a campaign.

### 3. Contactfom
####  A simple contact form will act as a service to provide additional information related to the book or workshop.

> People may want information , but not a newsletter.

### 
---

# 2. Scope 

### 1.  Migration and/or recreating of the gallery and **CMS**.

> Remake gallery, media, article-pages and CMS in **PHP/SQL**.
>
> Recover broken functions if needed and add new when requested (i.e. blog post , user engagements).
>
> The design and *UI/UX* of the Gallery , CMS and other pages will be near identical to orignal , but up to standards.


### 2. Database/CMS functionalities should be available for other upcoming affiliate websites

> More about Features below in section **4.2**

---

# 3. Structure

### 1.  Reading the directory/file structure of media content 

> There is 500mb+ of images , all in directorys with descriptive names and numbered files
>
> The descriptions in the directory names can be converted to a category record a database table;
>
>> directory " **the-dutch-landscape-** " is an example of what a *category* can be named and ..
>
>> in each category there are *.jpg* files with a bit of naming convention , like so;
>
>> - **2007-10.jpg**    <--- A majority of files are named starting with **year** and a **follow up number**
>
>> - **2007-3.jpg**     
>
>> - **2008-11-dutch-landscape-1.jpg**    <--- some may contain a descriptive **title**
>
>> - **2008-11-dutch-landscape-2.jpg**
>
>> - **2010-4--view-on-kockengen-0.jpg**
>
>> - **cda-2013-10-buitenhof-in-de-schemering.jpg** <--- and some make it tricky
>
>> - **2012-20-meadows-near-kockengen.jpg**   <--- we need also the whole name for the **url**
>


### 2. Use the data in the file-name(like above) by spliting and create for example :

| **year**  | **follow up number**  | **title**          | **url**                          |
| --------- | --------------------- | -----------------  |----------------------------------|
| 2007      | 10                    | *null*             |**2007-10.jpg**|
| 2007      | 3                     | *null*             |**2007-3.jpg**|
| 2008      | 11                    |  dutch-landscape-1 |**2008-11-dutch-landscape-1.jpg**|
| 2008      | 11                    |  dutch-landscape-2 |**2008-11-dutch-landscape-1.jpg**|
|           |                       |                    |


---


### 3. After Database and table are set create a basic edit (CMS) section for the admin(client).

> add CRUD functionality in a simple but effective interface.
>
>> **Create** 
>  Upload files , add data into tables etc.
>
>> **Read**
>     View all data (also for public users) , sorting command (a-z , category  ,etc).
>
>> **Update**
>   Edit data records , changing names, states etc.
>
>> **Delete**
>   Delete data
>

--- 

#### TODO:

1. [ ]  Scan dirs and files. 
2. [ ]  Sort and insert into db table.
3. [ ]  Basic CMS with CRUD functionality.
4. [ ]  ..?


---

# 4. Skeleton

### 1.  When the backend is functioning as it should we can improve UI/UX

#### TODO:

1. [ ]  Sanitize form inputs 
2. [ ]  Javascript DOM UI , modals for viewing post and other media
3. [ ]  ..?
 

### 2. Features to fix , make better or to add

##### to be concluded ..

---

# 5. Surface 

#### TODO:

1. [ ]  Replace bootstrap and other frameworks that make it look too generic
2. [ ]  Use CSS grid for the gallery to make it responsive
3. [ ]  ..?


> to be continued ...
>> 

---

