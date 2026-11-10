<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Modified Controllers, Switches and Joysticks, Adaptive Controllers, Controllers.
     */
    public function run(): void
    {
        $categoryIds = Category::pluck('id', 'name');
                $products = [
            // ==========================================
            // CONTROLLERS (8 produtos)
            // ==========================================
            [
                'category_id' => 'Controllers',
                'name' => '8BitDo 64 Bluetooth Controller Black',
                'description' => 'Retro-inspired layout based on the Nintendo 64 controller. Hall-effect joystick and durable metal joystick ring for longer lifespan. Connectivity: Bluetooth 5.0 or USB-C wired. Compatible with Nintendo Switch, Windows 10+, Android 9.0+. Supports turbo function and button mapping. Available in Black.',
                'price' => '24.50',
                'image' => 'https://accessiblegaming.be/wp-content/uploads/2025/10/8bitdo-64-bluetooth-controller-black.4468325834.cover_.webp',
                'stock' => '25',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => 'PlayStation DualSense™ Wireless Controller - PS5, PC, MAC & Mobile',
                'description' => 'Heighten Your Senses. Immersive haptic feedback, dynamic adaptive triggers, and built-in microphone, all integrated into an iconic comfortable design.',
                'price' => '75.00',
                'image' => 'https://m.media-amazon.com/images/I/51zRVFcqMLL._SX522_.jpg',
                'stock' => '15',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => 'XBOX Wireless Gaming Controller | Pulse Red',
                'description' => 'Built for Long Sessions, Ready for Every Screen. Sculpted surfaces and refined geometry for comfort that lasts through up to 40 hours of battery life. Pair wirelessly to console, Windows PC, mobile, and supported VR headsets. Includes Xbox Wireless and Bluetooth technology.',
                'price' => '55.00',
                'image' => 'https://m.media-amazon.com/images/I/61cf9SUgONL._SL1500_.jpg',
                'stock' => '88',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => 'Nintendo Switch™ 2 Pro Controller',
                'description' => 'Take your gaming to the next level. Features HD Rumble 2, motion controls, built-in amiibo functionality, Capture Button, C Button for GameChat, programmable GL/GR buttons, and audio jack.',
                'price' => '89.00',
                'image' => 'https://m.media-amazon.com/images/I/61nt5PSvJwL._SL1481_.jpg',
                'stock' => '64',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => 'Hori Flex Controller for Nintendo Switch & PC',
                'description' => 'Innovative accessible controller that allows players to use external switches and joysticks (sold separately) to play on Nintendo Switch and PC. Ideal for creating a personalized setup.',
                'price' => '265.00',
                'image' => 'https://hori.jp/image/2020/08/c64cf9f58a6e55f019d9fb542f98a1b5.png',
                'stock' => '2',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => 'ByoWave Proteus Builder One-Handed Gaming Controller',
                'description' => 'Advanced remapping and macro buttons, RGB lighting, ergonomic 3D printed shell. Designed for PC, Steam Deck & Xbox Series X|S. A modular controller that adapts to one-handed play.',
                'price' => '149.99',
                'image' => 'https://m.media-amazon.com/images/I/51nn4IuF9dL._SL1024_.jpg',
                'stock' => '10',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => 'Logitech G Adaptive Gaming Kit for Xbox Adaptive Controller',
                'description' => 'A set of durable, high-performance buttons and triggers for use with the Xbox Adaptive Controller (sold separately). Includes light-touch buttons, small and large mechanical buttons, and variable triggers.',
                'price' => '79.99',
                'image' => 'https://resource.logitechg.com/w_544,h_466,ar_7:6,c_pad,q_auto,f_auto,dpr_1.0/d_transparent.gif/content/dam/gaming/en/products/adaptive-gaming-kit/agk-gallery-1.png',
                'stock' => '30',
                'active' => '1',
            ],
            [
                'category_id' => 'Controllers',
                'name' => '8BitDo Lite SE Bluetooth Gamepad for Switch & Android',
                'description' => 'Ultra-portable Bluetooth gamepad with a lightweight design. Features Hall-effect joysticks and supports Switch, Android, and Raspberry Pi. Ideal as a secondary controller for accessible setups.',
                'price' => '29.99',
                'image' => 'https://i5.walmartimages.com/seo/8BitDo-Lite-SE-Bluetooth-Gamepad-for-Switch-Switch-Lite-Android-and-Raspberry-Pi-for-Gamers-with-Limited-Mobility_95598857-00b3-4041-9314-f192ca18cb64.ce35d9dd6889693fcaabea3768890ded.jpeg?odnHeight=640&odnWidth=640&odnBg=FFFFFF',
                'stock' => '0', // ESTOQUE ZERO
                'active' => '1',
            ],

            // ==========================================
            // ADAPTIVE CONTROLLERS (8 produtos)
            // ==========================================
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Microsoft Xbox & PC Adaptive Controller',
                'description' => 'The Xbox Adaptive Controller was developed to make gaming more accessible for people with limited mobility. Acts as a central hub to connect external devices via 3.5 mm inputs and USB ports. Works with Xbox One consoles and Windows PCs. Includes Xbox Wireless, Bluetooth, USB connectivity, Copilot, and a 3.5mm stereo headset jack.',
                'price' => '560.00',
                'image' => 'https://accessiblegaming.be/wp-content/uploads/2023/02/Manette-adaptative-pour-Xbox-Blanc-et-noir-3.jpg',
                'stock' => '500',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Hori Flex Controller (Accessible)',
                'description' => 'Accessible controller that allows gamers to connect external switches and joysticks to play on Nintendo Switch and PC. Supports a variety of assistive inputs for a fully customized experience.',
                'price' => '265.00',
                'image' => 'https://accessiblegaming.be/wp-content/uploads/2023/02/NintendoSwitch_02-800x533.jpg',
                'stock' => '2',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Pretorian Game On 1',
                'description' => 'A groundbreaking tool that offers gamers with disabilities full control over computer games using only their eyes, switches, or joysticks. Designed for users of AAC devices or PCs with eye-tracking. Compatible with standard PC games and Xbox games via the Xbox Adaptive Controller.',
                'price' => '470.00',
                'image' => 'https://www.pretorianuk.com/wp-content/uploads/2025/02/game-on-1-600x600.jpg',
                'stock' => '5',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Versatility v5 Microlite Lip or Fingertip Adaptive Game Controller',
                'description' => 'Microlight ultra-sensitive and ultra-responsive true proportional hall-effect joysticks requiring only 5 to 10 grams of force. Perfect for individuals with limited mobility (C1-C4 SCI, MD, SMA, ALS). Works as a game controller, mouse, or multi-button pad.',
                'price' => '851.95',
                'image' => 'https://inclusiveinc.org/cdn/shop/files/Versatility_v5_Dual_Hall-Effect_Micro_Lip_or_Fingertip_Joystick.jpg?crop=center&height=1200&v=1723320003&width=1200',
                'stock' => '1',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Ultimate Arcade 3 (UA3) Quadriplegic Adaptive Game Controller',
                'description' => 'Third-generation arcade controller reimagined for quadriplegic individuals with movement in one or both arms. Features two proportional arcade joysticks and 18 large accessible arcade buttons plus sip-n-puff or hands-free switches. Compatible with all major platforms via Titan Two.',
                'price' => '1929.00',
                'image' => 'https://inclusiveinc.org/cdn/shop/files/Untitleddesign-2024-01-27T031210.768.png?crop=center&height=1200&v=1724725463&width=1200',
                'stock' => '8',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Custom XAC and PAC Compatible Controllers',
                'description' => 'Custom Logitech Extreme 3D Pro adaptive joystick for PS4 with one-handed dual analog stick control, integrated PS4 controller, and accessible gaming buttons. Designed for use with Xbox Adaptive Controller (XAC) and PlayStation Access Controller (PAC).',
                'price' => '10.00',
                'image' => 'https://www.evilcontrollers.com/media/catalog/product/optimized/b/a/bacb6c5eacc8b0f25153bdb45b3924d827abaf7747be5b14771eb871217c2b48/2023-04-12_08.26.31.jpg',
                'stock' => '50',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Ben\'s Custom PlayBox XBX Controller',
                'description' => 'Custom Xbox Adaptive Controller interface with clustered ABXY button groups, large accessibility buttons, trigger and bumper controls, and dual thumbstick extensions. Designed to improve accuracy and comfort for adaptive gaming.',
                'price' => '10.00',
                'image' => 'https://www.evilcontrollers.com/media/catalog/product/optimized/3/0/303b448040a9e96b49135bb261a1f60c56ded1a12c010fad47da34cc75e652ec/img_6742.jpg',
                'stock' => '20',
                'active' => '1',
            ],
            [
                'category_id' => 'Adaptive Controllers',
                'name' => 'Xbox Adaptive Joystick (Official Microsoft Accessory)',
                'description' => 'Designed primarily for gamers with limited mobility. A wired joystick that connects via USB-C to Xbox consoles, Windows PCs, and other Xbox controllers. Features seven fully remappable buttons and a customizable thumbstick with 3D-printed attachments.',
                'price' => '29.99',
                'image' => 'https://www.microsoft.com/xbox-adaptive-joystick.jpg',
                'stock' => '40',
                'active' => '0', // PRODUTO INATIVO
            ],

            // ==========================================
            // SWITCHES AND JOYSTICKS (8 produtos)
            // ==========================================
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'KoG Mini Joystick – Gordon – Normal Stick',
                'description' => 'Keep on Gaming has developed a very lightweight gaming joystick that offers minimal resistance and is perfect for people with a muscle disease. Available in various sensitivities.',
                'price' => '503.00',
                'image' => 'https://accessiblegaming.be/wp-content/uploads/2023/11/Gordon_Joystick.png',
                'stock' => '55',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Pretorian Ultra Joystick',
                'description' => 'Exceptionally compact and extremely robust USB joystick, designed for users who rely on head or chin control. All-metal housing, industry-standard threaded mounting holes, two 3.5 mm jacks for external switches, programmable click functions, and four adjustable speed levels.',
                'price' => '510.00',
                'image' => 'https://www.pretorianuk.com/wp-content/uploads/2025/02/ultra-joystick-600x600.jpg',
                'stock' => '10',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Pretorian Quester Joystick',
                'description' => 'Compact and versatile desktop joystick designed for PC gaming. Ideal for players using WASD or arrow keys, with a precise mouse mode. Built-in mode switch, two outer buttons for left/right clicks, four customizable speed levels, and plug-and-play installation.',
                'price' => '325.00',
                'image' => 'https://accessiblegaming.be/wp-content/uploads/2023/03/quester_joystick_1-600x400.jpg',
                'stock' => '1',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Willow USB Joystick',
                'description' => 'Allows users with low force/range of motion to access their digital devices. Contains a low-force Hall-Effect joystick and a hub with a display for adjusting settings. Compatible with PC, Mac, Android, iOS, Windows, and Xbox Adaptive Controller. Requires 25 grams of force.',
                'price' => '120.00',
                'image' => 'https://www.makersmakingchange.com/sfsites/c/cms/delivery/media/MCQCWOYCRLFJBXVJKO64JJFRR2IE?version=1.1&channelId=0apJR0000000FNF',
                'stock' => '30',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Redwood USB Joystick',
                'description' => 'Robust analog USB joystick designed for users with gross motor movement and high strength. Durable build, functions as a computer mouse or gamepad. Compatible with phones, computers, Xbox Adaptive Controller, and Hori Flex Controller.',
                'price' => '75.00',
                'image' => 'https://www.makersmakingchange.com/sfsites/c/cms/delivery/media/MCEJMGFKVFTFC7FFVTUELYCJJZGU?version=1.1&channelId=0apJR0000000FNF',
                'stock' => '25',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Aspen Sliding Joystick',
                'description' => 'Small analog joystick with a very small range of motion (sliding motion instead of pivoting). Can be used directly with Xbox Adaptive Controller (XAC) or PlayStation Access Controller, or with the Forest Joystick Mouse Hub. Requires approximately 115 grams-force to fully deflect.',
                'price' => '35.00',
                'image' => 'https://www.makersmakingchange.com/sfsites/c/cms/delivery/media/MC6FCR5YEYBNEUDNH64ZQFRYOGV4?version=1.1&channelId=0apJR0000000FNF',
                'stock' => '60',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Forest Joystick Mouse Hub',
                'description' => 'Enables a user to connect an analog joystick and up to four assistive switches to emulate a USB Mouse or USB Gamepad. Compatible with computers, tablets, mobile phones, and the Xbox Adaptive Controller.',
                'price' => '80.00',
                'image' => 'https://www.makersmakingchange.com/sfsites/c/cms/delivery/media/MC5MWSJDD75ZDQ3ABUARJDIENGBU?version=1.1&channelId=0apJR0000000FNF',
                'stock' => '18',
                'active' => '1',
            ],
            [
                'category_id' => 'Switches and Joysticks',
                'name' => 'Spruce Mini Joystick',
                'description' => 'Small analog joystick with a range of motion of ±30° and a built-in switch activated by pressing down on the topper. Requires approximately 75 grams-force. Can be used with Xbox Adaptive Controller, PlayStation Access Controller, or Forest Hub.',
                'price' => '40.00',
                'image' => 'https://www.makersmakingchange.com/sfsites/c/cms/delivery/media/MCS34R5SL65FHI5AY5QS33RXR7SI?version=1.1&channelId=0apJR0000000FNF',
                'stock' => '45',
                'active' => '1',
            ],

            // ==========================================
            // Modified Controllers (8 produtos)
            // ==========================================
            [
                'category_id' => 'Modified Controllers',
                'name' => 'ViGrip 2 - One-Handed Joy-Con 2 Controller Adapter (Nintendo Switch 2)',
                'description' => 'Accessory compatible with the Joy-Con 2 of the Nintendo Switch 2 console, enabling you to build an accessible controller you can operate with one hand. Snap your Joy-Con 2 into place magnetically. Includes an adjustable wrist strap to prevent accidental drops.',
                'price' => '216.09',
                'image' => 'https://i.ebayimg.com/images/g/7EUAAeSwmmRob-jY/s-l960.png',
                'stock' => '4',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'One-Handed PlayStation 4 DualShock 4 Controller Mod',
                'description' => '3D-printed controller mod that allows a PlayStation 4 DualShock 4 controller to be used with one hand. Resting the controller on a surface allows for control of the secondary joystick when you move the controller, while the D-pad and buttons remain accessible.',
                'price' => '45.00',
                'image' => 'https://thecontrollerproject.com/ps4-one-hand.jpg',
                'stock' => '12',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'One-Handed Xbox Series X/S Controller Mod',
                'description' => '3D-printed controller mod that allows an Xbox Series X/S controller to be used with one hand. Resting the controller on a surface or your lap allows for control of the secondary joystick when you move the controller, while the D-pad or other inputs are reached with the same hand.',
                'price' => '50.00',
                'image' => 'https://thecontrollerproject.com/xbox-one-hand.jpg',
                'stock' => '15',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'One-Handed DualSense (PS5) Controller Mod',
                'description' => '3D-printed controller mod that allows a PlayStation 5 DualSense controller to be used with one hand. Resting the controller on a surface or your lap allows for control of the secondary joystick when you move the controller, while the D-pad or △OX▢ buttons remain accessible.',
                'price' => '55.00',
                'image' => 'https://thecontrollerproject.com/ps5-one-hand.jpg',
                'stock' => '9',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'One-Handed Joy-Con Grip (Sheikah Style)',
                'description' => 'This 3D-printed mod allows for both Nintendo Switch Joy-Cons to be used one-handed. Designed for both left-handed and right-handed players. Ideal for accessible Switch gaming.',
                'price' => '38.00',
                'image' => 'https://thecontrollerproject.com/wp-content/uploads/2022/04/JoyConGripSheikahStyle-768x487.png',
                'stock' => '22',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'Custom PS4 Controller with 4 Paddles on Faceplate',
                'description' => 'Custom PS4 accessible controller featuring four top-mounted shift paddles that relocate the shoulder and trigger inputs to a more accessible position. Designed for tabletop gameplay.',
                'price' => '120.00',
                'image' => 'https://www.evilcontrollers.com/ps4-4-paddles.jpg',
                'stock' => '6',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'Custom PS5 Accessible PlayBox Controller (Ryan\'s Layout)',
                'description' => 'Compact adaptive gaming solution built around a fully integrated wireless PS5 controller. Features low-force 1.3N activation buttons and a layout customized for accessibility.',
                'price' => '250.00',
                'image' => 'https://www.evilcontrollers.com/media/catalog/product/optimized/2/d/2d106617ac1d1a43f8daa8b58e46c821b7c5df9f550465297c7787aca2237f8e/20250417_172935.jpg',
                'stock' => '3',
                'active' => '1',
            ],
            [
                'category_id' => 'Modified Controllers',
                'name' => 'Xbox 360 Split Gaming Controller (Custom Mod)',
                'description' => 'Custom split controller design that separates the Xbox 360 controller into two independent halves, allowing each hand to be positioned separately. Ideal for users who cannot bring their hands together.',
                'price' => '180.00',
                'image' => 'https://www.evilcontrollers.com/media/catalog/product/optimized/d/7/d7daba3e3b8ce4f37a717a5660ae8d2eb16b2248da32c1cae46d301122492c97/split_con.jpg',
                'stock' => '5',
                'active' => '1',
            ],
        ];

        foreach($products as $product){
            $product['category_id'] = $categoryIds[$product['category_id']];
            Product::create($product);
        }
    }
}
