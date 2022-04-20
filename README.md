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
 - Là một kỹ thuật tấn công SQL Injection dựa vào thông báo lỗi được trả về từ Database Server có chứa thông tin về cấu trúc của cơ sở dữ liệu. Trong một vài trường hợp, chỉ một mình Error-based là đủ cho hacker có thể liệt kê được các thuộc tính của cơ sở dữ liệu.

<br> 1.2 Khi nào cần phải sử dụng Error-based sqli <a name="tc"></a></br>
 - Đó là khi chúng ta không thể nhận được bất kỳ đầu ra nào bằng cách sử dụng Union base sqli và lỗi sẽ hiển thị cho chúng ta. Trong trường hợp đó, chúng ta phải sử dụng tính năng error base sqli.

#### 2. Chi tiết về lỗi sql injection <a name="gioithieu"></a>
<br> 1.1 Khai thác lỗi sqli dựa trên Numberic overflow <a name="tc"></a></br>
 - MySQL hỗ trợ các kiểu dữ liệu tiêu chuẩn SQL như INTEGER, SMALLINT, TINYINT, MEDIUMINT, BIGINT. FLOAT, DOUBLE…
 - Mỗi kiểu dữ liệu sẽ có một phạm vi giới hạn khác nhau. Khi các giá trị vượt ra ngoài giới hạn này nó sẽ dẫn tới hiện tượng tràn số (overflow). Có sự khác nhau trong xử lý Out-of-range và overflow giữa các phiên bản MySQL. Việc xử lý Out-of-range và overflow từ phiên bản MySQL 5.5.5 trở lên sẽ trả về một thông báo lỗi, trong khi trong các phiên bản thấp hơn mặc định sẽ trả về một kết quả mà không đưa ra bất kỳ thông báo lỗi nào.
  - Ví dụ, kiểu dữ liệu BIGINT có kích thước 8 byte (64 bit). Giá trị số nguyên có dấu lớn nhất của nó là:

    - Binary: 0b0111111111111111111111111111111111111111111111111111111111111111
    - Hex: 0x7fffffffffffffff
    - Decimal: 9223372036854775807
  
    - MySQL 5.5.5 trở lên: Khi thực hiện phép toán với các giá trị trên (ví dụ tăng thêm 1) sẽ gây ra tràn số và trả về thông báo lỗi:
      ![image](https://user-images.githubusercontent.com/101852647/164141571-0337b871-260d-403c-bb43-c2f174b01681.png)
    - Trong phiên bản MySQL thấp hơn, kết quả trả về một giá trị:
