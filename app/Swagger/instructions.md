For Installation go to: https://zircote.github.io/swagger-php/ 
Documentation Link: https://zircote.github.io/swagger-php/

1. Install the zircote/swagger-php composer package
2. Install the doctrine/annotations composer package
3. Put the Webby-Swagger/Swagger.php file in app/Controllers directory
4. Put the Webby-Swagger/Swagger folder files in app/Swagger directory
5. Put the Webby-Swagger/dist folder in your public/assets/dist directory
6. Put the Webby-Swagger/swagger-ui folder in your app/Views directory
7. Change app.baseURL = 'http://example.domain:port_number/' in file the .env file
8. You can now create namespaced classes in the main directory or Controllers and DataModels directory for seperation of concerns
9. In case you get errors concerning  "Internal Server Error" then go to writetable/logs/system/log-20**-**-**.php file to see the last error list 
10. Mostly it might be an annotation error or a namespace error. 
11. If any other erros contact me on +233243721004 or developerkwame@gmail.com

---
A helping project that helped me to figure this out
https://github.com/manish29ify/codeigniter3-restapi-with-swagger