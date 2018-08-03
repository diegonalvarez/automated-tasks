Group of tasks that automate process

## Getting Started

To start is necessary rename the file `.env.sample` to `.env`.

Set the correct credentials depending on the project that you are running.

## Jira Update Status Tasks (Tasks/Jira/UpdateTasks/UpdateTasks.php)

This task allows update Jira issues to a specific transition(status). We used to change the status of the task per Due Date condition, avoiding missings issues in Jira.

### Update the .env file

Set the JIRA variables in the .env file

### Config File

There is a config file in `Tasks/Jira/UpdateTasks/config.sample.cfg`. First copy the file accordingly your environment, for example:

- config.production.cfg
- config.development.cfg

Then set the variables:

#### [issue_days]

The ammount of days to find issues from the current date.

#### [users]

The users that need to be analyzed. In a future we are going to use a group and getting directly from Jira.

#### [project_transition_id]

Set the Project and the transition ID. For example if you have multiples projects with differentes workflows you can set

PROJECT_ONE   = 1
PROJECT_TWO   = 2

#### [avoid_status]

There are issues that already had the status, so we avoid to update the issue but still we wanna see the issue in the log. You can exclude this status from the query if you don't need to see the issue ID.
