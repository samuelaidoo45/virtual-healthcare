<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Appointment;
use App\Http\Resources\DoctorsAppointmentResource;
use App\Doctor;
use App\Http\Requests\MakePrescriptionRequest;

use App\Prescription;
class DoctorsController extends Controller
{

    public function __construct()
    {
        $this->middleware(['multiauth:doctor']);
    }
    
    // List appointment
    public function list_appointment(Doctor $doctor){
        
    
        
        // $approved = $doctor->appointments->where('approved', true);

        // $unapproved = $doctor->appointments->where('approved', false);

        $appointment = $doctor->appointments;

        // $appointment = [

        //     "unapproved" => $unapproved,
        //     "approved" => $approved
        // ];

         if ($appointment->count() > 0) {

           return DoctorsAppointmentResource::collection($appointment);

        } else {

            return response()->json([
                'message' => 'No appointments'
            ]);
        } 
    }

    //Approve appointment
    public function approve_appointment(Appointment $appointment){
            $appointment->approved = true;

            $appointment->save();

            return response()->json([
                'message' => 'Appointment approved.'
            ]);
    }

    //Make prescription
    public function make_prescription(MakePrescriptionRequest $request){
        $prescription = Prescription::create([
            'user_id' => $request->user_id,
            'doctor_id' => auth()->guard('doctor')->id(),
            'case_history' => $request->case_history,
            'medication' => $request->medication,
            // 'medication_from_pharmacist' => $request->medication_from_pharmacist,
        ]);

        return response()->json([
            'prescription' => $prescription,
        ]);
    }
}
