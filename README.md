# Installation
1. Ensure that the following are installed on your system: Docker, Docker Compose, Make, Git.
2. Run: git clone https://github.com/nrnwest/todo_list_api.git.
3. cd todo_list_api.
4. Initialize the project by running: make init.
5. Go to the project's web address, [todo_list_api](http://localhost:4441). Open the Authentication tab, log in using 
   the default credentials, and click "Execute." Once you receive the token, enter it into "Authorize" in the 
   upper-right corner of the screen.
6. Test the API in Swagger.
7. Run PHPUnit tests with: make test.

**Project Description and Solved Tasks:**

1. Retrieve a list of your tasks according to the filter
2. Create your own task
3. Edit your own task
4. Mark your own task as completed
5. Delete your own task

When retrieving the list of tasks, the user should be able to:

- Filter by the `status` field
- Filter by the `priority` field
- Filter by the `title` and `description` fields (full-text search should be implemented)
- Sort by `createdAt`, `completedAt`, `priority` - support for sorting by two fields is needed. For example, `priority desc`, `createdAt asc`.

The user should not be able to:

- Modify or delete others' tasks
- Delete already completed tasks
- Mark a task as completed if it has incomplete subtasks

Each task should have the following properties:

- `status` (todo, done)
- `priority` (1...5)
- `title`
- `description`
- `createdAt`
- `completedAt`

**Additional Requirements:**

- Use Docker
- Utilize the advantages of PHP 8
- Follow SOLID and KISS principles
- Apply best practices of OOP

Any task can have subtasks, and the level of nesting for subtasks should be unlimited. Everything must be tested.

