 # <div align="center"><p> Error-base-sql-injection </p></div>
 ## Họ và tên: Mai Thị Hoàng Yến
 ## Ngày báo cáo: Ngày 20/4/2022
 ### MỤC LỤC
 1. [Tìm hiểu về error base sql injection](#gioithieu)
 
     1.1 [Phương pháp manual](#tc)
      
     1.2 [Phương pháp sử dụng sqlmap](#pp)
 
     1.3 [Phương pháp sử dụng công cụ BurpSuite](#p3)
     
 2. [Chi tiết về lỗi sql injection](#mp) 
       
 3. [Thực hành các lỗi sql injection](#lv)

     3.1 [Code sửa lỗi sqli cho level2](#code1)
      
     3.2 [Code sửa lỗi sqli cho level1](#code2)
     
     3.3 [Các hàm sử dụng](#chsd)
 
### Nội dung báo cáo 
#### 1. Tìm hiểu về error base sql injection <a name="gioithieu"></a>
<br> 1.1 Khái niệm <a name="tc"></a></br>
 - Là một kỹ thuật tấn công SQL Injection dựa vào thông báo lỗi được trả về từ Database Server có chứa thông tin về cấu trúc của cơ sở dữ liệu. Trong một vài trường hợp, chỉ một mình Error-based là đủ cho hacker có thể liệt kê được các thuộc tính của cơ sở dữ liệu
<br> 1.2 Khi nào cần phải sử dụng Error-based sqli <a name="tc"></a></br>
 - Đó là khi chúng ta không thể nhận được bất kỳ đầu ra nào bằng cách sử dụng tiêm dựa trên Union và lỗi sẽ hiển thị cho chúng ta. Trong trường hợp đó, chúng ta phải sử dụng tính năng tiêm dựa trên lỗi.
