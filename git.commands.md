https://stackoverflow.com/questions/34850831/change-git-email-for-previous-commits/34851024#34851024

git filter-branch --commit-filter '

    # check to see if the committer (email is the desired one)
    if [ "$GIT_COMMITTER_EMAIL" = "stevkky@gmail.com" ];
    then
            # Set the new desired name
            GIT_COMMITTER_NAME="otengkwame";
            GIT_AUTHOR_NAME="otengkwame";

            # Set the new desired email
            GIT_COMMITTER_EMAIL="developerkwame@gmail.com";
            GIT_AUTHOR_EMAIL="developerkwame@gmail.com";

            # (re) commit with the updated information
            git commit-tree "$@";
    else
            # No need to update so commit as is
            git commit-tree "$@";
    fi' 
HEAD

git filter-branch --env-filter 'if [ "$GIT_AUTHOR_EMAIL" = "otengkwameit@gmail.com" ]; then
     GIT_AUTHOR_EMAIL=developerkwame@gmail.com;
     GIT_AUTHOR_NAME="otengkwame";
     GIT_COMMITTER_EMAIL=$GIT_AUTHOR_EMAIL;
     GIT_COMMITTER_NAME="$GIT_AUTHOR_NAME"; fi' -- --all

### Claim Repo

git remote -v
# View existing remotes
# origin  https://github.com/user/repo.git (fetch)
# origin  https://github.com/user/repo.git (push)

git remote set-url origin https://github.com/user/repo2.git
# Change the 'origin' remote's URL

git remote -v
# Verify new remote URL
# origin  https://github.com/user/repo2.git (fetch)
# origin  https://github.com/user/repo2.git (push)


* After this push `git push remote origin main -f`
* Then do `git push origin HEAD:main`
* then you are done but if there are any issues 
* You can `git pull origin master --allow-unrelated-histories` to allow and finally push
* You can set upstream `git push --set-upstream origin`

