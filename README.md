<h1># PHP Test Task</h1>

1. You need to upload the project to a public repository in gitlab
2. The test project must be run in docker
3. You need to create a readme page about starting and using the project

Develop a service for working with a dataset

Initial data:
.csv dataset
     'category', // client's favorite category
     'firstname',
     'lastname',
     'email',
     'gender',
     'birthDate'

Without using third party libraries:
Read csv file.

Write the received data to the database.

Display data as a table with pagination (but you can also use a simple json api)

Implement filters by values:
     category
     gender
     Date of Birth
     age
     age range (for example, 25 - 30 years)

Implement data export (in csv) according to the specified filters.
https://drive.google.com/file/d/1Dwb1alDAQCAPwz7Eg306BVbWtGdfkUCy/view?usp=sharing

<h2>## Getting Started</h2>

To get started with this project, you will need to do the following:

1. Clone the project from the public repository on GitLab:

```
git clone [https://gitlab.com/good-guy1218/test-project.git]
```

2. Install Docker on your local machine.

3. Go to the project folder and run the following command to build and start the Docker containers:

```
docker-compose up --build
```

4. You should now be able to access the service by visiting `http://localhost:8080` in your web browser.

<h2>## Usage</h2>

The service allows you to work with CSV files and display data as a table with pagination. You can filter the data by category, gender, date of birth, age, and age range.

To use the service, follow these steps:

1. Upload a CSV file to the service by clicking on the "Choose File" button on the home page.

2. Once the file is uploaded, you can view the data in a table with pagination.

3. Use the filter options to filter the data by category, gender, date of birth, age, and age range.

4. Click on the "Export CSV" button to export the filtered data as a CSV file.

<h2>## Contributing</h2>

If you would like to contribute to this project, please fork the repository and submit a pull request.

<h2>## License</h2>

This project is licensed under the [MIT License](LICENSE).